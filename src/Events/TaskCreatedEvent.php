<?php

namespace Spork\Planning\Events;

class TaskCreatedEvent
{
    public $task;

    public function __construct($task)
    {
        $this->task = $task;
    }
}