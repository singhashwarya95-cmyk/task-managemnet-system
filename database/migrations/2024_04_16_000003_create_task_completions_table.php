<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('screenshots');
            $table->text('remarks');
            $table->enum('verification_status', ['Pending', 'Verified', 'Rejected'])->default('Pending');
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_completions');
    }
};
