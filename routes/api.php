<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'getAuthenticatedUser']);
    Route::post('/send_email', [TaskController::class, 'send_email']);

    Route::post('/tasks', [TaskController::class, 'createTask']);
    Route::get('/tasks', [TaskController::class, 'getUserTasks']);
    Route::put('/tasks/{task}/complete', [TaskController::class, 'markComplete']);
    Route::delete('/tasks/{id}', [TaskController::class, 'deleteTask']);
    Route::put('/tasks/{id}', [TaskController::class, 'updateTask']);

    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    
    Route::post('/tasks/emsend', [TaskController::class, 'emsend']);

});