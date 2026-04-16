<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('action_type', ['Create', 'Update', 'Delete', 'Completion']);
            $table->json('old_data')->nullable();
            $table->json('new_data');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_requests');
    }
};
