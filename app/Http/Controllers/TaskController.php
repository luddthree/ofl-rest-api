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
            'assigned_to' => 'required|email|exists:users,email',
            'deadline' => 'nullable|date|after_or_equal:today' // Ensure the deadline is in the future
        ]);
    
        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
            'assigned_to' => $validated['assigned_to'],
            'deadline' => $validated['deadline'] ?? null,
        ]);
    
        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }
    

    public function getUserTasks()
    {
        $userEmail = Auth::user()->email;
        $userId = Auth::id();
    
        $assignedTasks = Task::where('assigned_to', $userEmail)->get();
        $createdTasks = Task::where('created_by', $userId)->get();
    
        return response()->json([
            'assigned_tasks' => $assignedTasks,
            'created_tasks' => $createdTasks
        ]);
    }

    public function deleteTask($id)
{
    $task = Task::findOrFail($id);

    // Ensure only the creator can delete the task
    if ($task->created_by !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $task->delete();
    return response()->json(['message' => 'Task deleted successfully']);
}


public function updateTask(Request $request, $id)
{
    $task = Task::findOrFail($id);

    if ($task->created_by !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'title' => 'required|string',
        'description' => 'nullable|string',
        'assigned_to' => 'required|email|exists:users,email',
        'deadline' => 'nullable|date|after_or_equal:today' // Ensure valid deadline
    ]);

    $task->update($validated);

    return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
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
