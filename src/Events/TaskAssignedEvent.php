<?php

namespace Spork\Planning\Events;

class TaskAssignedEvent
{
    public $task;

    public function __construct($task)
    {
        $this->task = $task;
    }
}
