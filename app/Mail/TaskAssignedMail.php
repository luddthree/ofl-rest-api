<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
                    ->subject('New Task Assigned')
                    ->view('emails.task_assigned')
                    ->with(['task' => $this->task]);
    }
}
