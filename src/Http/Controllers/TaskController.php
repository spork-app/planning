<?php

namespace Spork\Planning\Http\Controllers;

use Illuminate\Http\Request;

class TaskController
{
    public function statuses()
    {
        return \Spatie\QueryBuilder\QueryBuilder::for(\Spork\Planning\Models\Status::class)
        ->allowedIncludes(['users', 'tasks', 'tasks.creator', 'tasks.assignee'])
        ->where('user_id', auth()->id())
        ->get();
    }

    public function users()
    {
        return \Spatie\QueryBuilder\QueryBuilder::for(\App\Models\User::class)
        ->allowedIncludes(['tasks.creator', 'tasks.assignee'])
        ->get();
    }

    public function assignTask(Request $request)
    {
        $task = \Spork\Planning\Models\Task::findOrFail($request->get('task_id'));
        $user = \App\Models\User::findOrFail($request->get('user_id'));
        $task->assignee_id = $user->id;
        $task->save();
    }

    public function sync(Request $request)
    {
        $request->validate([
            'columns' => ['required', 'array'],
        ]);

        foreach ($request->columns as $status) {
            foreach ($status['tasks'] as $i => $task) {
                $order = $i + 1;
                if ($task['status_id'] !== $status['id'] || $task['order'] !== $order) {
                    \Spork\Planning\Models\Task::find($task['id'])
                        ->update(['status_id' => $status['id'], 'order' => $order]);
                }
            }
        }

        return $request->user()->statuses()->with('tasks')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable',
            'order' => 'required|int',
            'status_id' => 'required|exists:statuses,id',
        ]);

        /** @var \App\Core\Models\User $user */
        $user = $request->user();

        return $user->tasksCreated()->create($request->all());
    }

    public function destroy(Request $request, $id)
    {
        $task = \Spork\Planning\Models\Task::findOrFail($id);
        $task->delete();

        return response()->json([], 204);
    }
}
