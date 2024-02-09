<?php

/*
 * This file is part of the charonlab/charon-events.
 *
 * Copyright (C) 2024 Charon Lab Development Team
 *
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE.md file for details.
 */

namespace Charon\Tests\Unit;

use Charon\Events\Event;
use Charon\Events\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventDispatcher::class)]
#[Group('unit')]
class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;
    public function testDispatchShouldInvokeListener(): void {
        $invoked = false;

        $this->dispatcher->listen('test.event', function () use (&$invoked) {
            $invoked = true;
        });

        $this->dispatcher->dispatch(new class{}, 'test.event');

        self::assertTrue(
            $invoked,
            'Listener should be invoked on event dispatch.'
        );
    }

    public function testDispatchShouldNotInvokeListenerAfterPropagationStopped(): void {
        $invokedAfterStop = false;

        $this->dispatcher->listen('test.event', function (Event $event) {
            $event->setPropagationStopped();
        });

        $this->dispatcher->listen('test.event', function () use (&$invokedAfterStop) {
            $invokedAfterStop = true;
        });

        $this->dispatcher->dispatch(new Event(), 'test.event');

        self::assertFalse(
            $invokedAfterStop,
            'Listener should not be invoked after propagation is stopped'
        );
    }

    public function setUp(): void {
        $this->dispatcher = new EventDispatcher();
    }
}
