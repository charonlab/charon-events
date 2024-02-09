<?php

/*
 * This file is part of the charonlab/charon-events.
 *
 * Copyright (C) 2024 Charon Lab Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE.md file for details.
 */

namespace Charon\Events;

use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var callable[][][]  */
    protected array $listeners = [];
    /** @var callable[][] $sortedListeners */
    protected array $sortedListeners = [];

    /**
     * @inheritDoc
     */
    public function listen(string $eventName, callable $listener, int $priority = 10): void {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sortedListeners[$eventName]);
    }

    /**
     * @inheritDoc
     */
    public function dispatch(object $event, ?string $eventName = null): object {
        $eventName ??= $event::class;

        if (($listeners = $this->getListeners($eventName)) !== []) {
            $this->invokeListeners($listeners, $event, $eventName);
        }

        return $event;
    }

    /**
     * @inheritDoc
     */
    public function getListeners(string $eventName): iterable {
        if ($this->listeners[$eventName] === []) {
            return [];
        }

        if (!$this->sortedListeners[$eventName]) {
            $this->sortListeners($eventName);
        }

        return $this->sortedListeners[$eventName];
    }

    /**
     * @param callable[] $listeners
     *  The event listeners.
     * @param object $event
     *  The event object to pass to the listener.
     * @param string $eventName
     *  The event name.
     *
     * @return void
     */
    protected function invokeListeners(iterable $listeners, object $event, string $eventName): void {
        foreach ($listeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }
    }

    /**
     * Sorts the event listeners by priority.
     *
     * @param string $eventName
     *  The event name.
     *
     * @return void
     */
    protected function sortListeners(string $eventName): void {
        \ksort($this->listeners[$eventName]);
        $this->sortedListeners[$eventName] = [];

        foreach ($this->listeners[$eventName] as &$listeners) {
            foreach ($listeners as &$listener) {
                $this->sortedListeners[$eventName][] = $listener;
            }
        }
    }
}
