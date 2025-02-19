<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskAssignedMail;
use Illuminate\Support\Facades\Log;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require_once 'vendor/autoload.php'; // If installed via Composer



class TaskController extends Controller
{
    
    public function createTask(Request $request)
    {
        Log::info('assignTazzzzzzzzsk method triggered'); 
// legger til oppgave i databasen
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'assigned_to' => 'required|email|exists:users,email',
            'deadline' => 'nullable|date|after_or_equal:today' 
        ]);
    
        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
            // 'created_by' => Auth::user()->email, 
            'assigned_to' => $validated['assigned_to'],
            'deadline' => $validated['deadline'] ?? null,
        ]);


// sende epost til brukern som får oppgaven
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io'; // Change to your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fa0f2c40f7fd3f'; // Your Mailtrap/Gmail SMTP username
            $mail->Password   = '65af621d168667'; // Your Mailtrap/Gmail SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
            $mail->Port       = 587; // SMTP port

            // Sender & Recipient
            $mail->setFrom('noreply@ofl.no', 'OFL');
            $mail->addAddress($validated['assigned_to']); 

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = 'New Task Assigned: ' . $validated['title'];
            $mail->Body    = '<h1>New Task Assigned</h1><p>Log in to the <a href="http://localhost:3000/dashboard">dashboard</a> to view you new task!</p>'
                . '<p>Task Description: ' . $validated['description'] . '</p>';

            // Send Email
            if ($mail->send()) {
                echo "Email Sent Successfully!";
            } else {
                echo "Failed to Send Email!";
            }
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    
        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }
    

    public function getUserTasks()
    {
        // Henter oppgaver som er tildelt brukeren eller oppgaver som brukeren har opprettet
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
    Log::info ('id'. $id);

    Log::info ('auth id'. Auth::id());

    $task = Task::findOrFail($id);
    Log::info ('task id'. $task->created_by);

    if ($task->created_by !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $task->delete();
    return response()->json(['message' => 'Task deleted successfully']);
}
    
    public function updateTask(Request $request, $id)
    {
        // Update task information
        $task = Task::findOrFail($id);
    
        // Ensure only the creator (by email) can update the task
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
    // merker oppgave som fullført
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
    
    public function emsend(Request $request)
    // sender epost
    {

        log::info ('send email method triggered');

        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io'; // Change to your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fa0f2c40f7fd3f'; // Your Mailtrap/Gmail SMTP username
            $mail->Password   = '65af621d168667'; // Your Mailtrap/Gmail SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
            $mail->Port       = 587; // SMTP port

            // Sender & Recipient
            $mail->setFrom('noreply@example.com', 'Your App');
            $mail->addAddress('luddetv@gmail.com', 'Ludvik'); // Change to the recipient

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = 'Task Assigned';
            $mail->Body    = '<h1>New Task Assigned</h1><p>Finish project report by 2025-02-10</p>';

            // Send Email
            if ($mail->send()) {
                echo "Email Sent Successfully!";
            } else {
                echo "Failed to Send Email!";
            }
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
            
                return response()->json(['message' => 'Email sent successfully']);
    }


}
