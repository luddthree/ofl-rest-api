<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class MarkOverdueTasks extends Command
{
    protected $signature = 'tasks:mark-overdue';
    protected $description = 'Mark tasks as overdue if they are past their deadline';

    public function handle()
    {
        $overdueTasks = Task::where('completed', false)
                            ->whereNotNull('deadline')
                            ->where('deadline', '<', Carbon::now())
                            ->get();

        foreach ($overdueTasks as $task) {
            $task->update(['completed' => false]); // Mark as overdue (or add a new column for "overdue")
        }

        $this->info(count($overdueTasks) . ' tasks marked as overdue.');
    }
}
