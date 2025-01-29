<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'assigned_to' => 'required|email|exists:users,email', // Ensure user exists
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
            // 'created_by' => Auth::user()->name,
            'assigned_to' => $validated['assigned_to'],
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function getUserTasks()
    {
        $userEmail = Auth::user()->email;
        $tasks = Task::where('assigned_to', $userEmail)->get();

        return response()->json(['tasks' => $tasks]);
    }

    public function completeTask($id)
    {
        $task = Task::findOrFail($id);
        $task->update(['completed' => true]);

        return response()->json(['message' => 'Task marked as completed', 'task' => $task]);
    }

    public function markComplete(Task $task)
    {
        $task->completed = true;
        $task->save();
    
        return response()->json(['message' => 'Task marked as complete', 'task' => $task]);
    }
    


}
