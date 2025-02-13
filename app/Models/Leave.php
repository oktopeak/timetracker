<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $table = 'leaves';
    public $timestamps = false;

    protected $casts = [
        'leave_start' => 'datetime',
        'leave_end' => 'datetime'
    ];

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'leave_start',
        'leave_end',
        'notes',
        'status',
        'number_of_days',
        'reviewed_by',
        'reviewed_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function leave_type(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}
