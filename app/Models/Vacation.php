<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    protected $table = 'vacations';

    protected $fillable = [
        'user_id',
        'number_of_days',
        'year',
        'days_left',
        'carried_days'
    ];

    protected $attributes = [
        'days_left' => 0,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
