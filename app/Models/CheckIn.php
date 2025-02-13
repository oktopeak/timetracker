<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    protected $table = 'check_ins';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'date' => 'datetime',
    ];
}
