<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'name',
        'team_leader_id',
        'created_by',
        'is_active'
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function members() {
        return $this->belongsToMany(User::class, 'team_members', 'team_id', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'team_id');
    }
}
