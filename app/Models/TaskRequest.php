<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'action_type',
        'old_data',
        'new_data',
        'status',
    ];

    protected $casts = [
        'old_data' => 'json',
        'new_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
