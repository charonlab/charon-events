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

class Event implements StoppableEventInterface
{
    protected bool $propagationStopped = false;

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool {
        return $this->propagationStopped;
    }

    public function setPropagationStopped(): void {
        $this->propagationStopped = true;
    }
}
