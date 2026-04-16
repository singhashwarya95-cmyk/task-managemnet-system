<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'deadline',
        'approval_status',
        'admin_remarks',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completions()
    {
        return $this->hasMany(TaskCompletion::class);
    }

    public function requests()
    {
        return $this->hasMany(TaskRequest::class);
    }

    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class);
    }

    public function getStatusColorAttribute()
    {
        if ($this->status === 'Completed') {
            return $this->approval_status === 'Verified' ? 'D5E8D4' : 'F8CECC';
        }

        if ($this->deadline && now() > $this->deadline) {
            return 'F8CECC'; // Red
        }

        return 'FFF2CC'; // Yellow
    }
}
