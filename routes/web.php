<?php

use Illuminate\Support\Facades\Route;
use Spork\Planning\Http\Controllers\TaskController;

Route::get('status', [TaskController::class, 'statuses']);
Route::get('users', [TaskController::class, 'users']);

Route::post('assign-task', [TaskController::class, 'assignTask']);

Route::put('sync', [TaskController::class, 'sync']);

Route::apiResource('tasks', TaskController::class);
