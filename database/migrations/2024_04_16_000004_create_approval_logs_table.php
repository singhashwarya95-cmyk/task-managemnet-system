<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['Approved', 'Rejected', 'Verified', 'Rejected Completion']);
            $table->text('remarks')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('approval_logs');
    }
};
