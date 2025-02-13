<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_types';
    const SICK_LEAVE_ID = 1;

    const SICK_LEAVE = "sick_leave";
    const ANNUAL_LEAVE = "annual_leave";
    const PERSONAL_LEAVE = "personal_leave";
    protected $guarded = ['id'];
    public $timestamps = false;


    public function leaves()
    {
        return $this->hasMany(Leave::class, 'leave_type_id');
    }
}
