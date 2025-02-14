<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('created_by'); // Store username instead of user ID
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->string('assigned_to');
            $table->timestamps();
            $table->boolean('completed')->default(false);

            // Foreign key constraint
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
