<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'admin_id',
        'action',
        'remarks',
        'old_data',
        'new_data',
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

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
