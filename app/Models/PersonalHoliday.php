<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalHoliday extends Model
{
    protected $table = 'personal_holidays';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name', 
        'date'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
