<?php

namespace Spork\Planning\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;

class CreateDefaultStatuses
{
    protected const STATUSES = [
        'Backlog',
        'To do',
        'In Progress',
        'Done',
    ];

    public function handle(Registered $event)
    {
        $this->createStatuses($event->user);
    }

    protected function createStatuses(User $user)
    {
        foreach (static::STATUSES as $order => $status) {
            $user->statuses()->create([
                'title' => $status,
                'slug' => Str::of($status)->slug(),
                'order' => $order,
            ]);
        }
    }
}
