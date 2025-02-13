<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'phone_number',
        'email',
        'position',
        'birthday',
        'created_at',
        'password',
        'pin',
        'role',
        'position_id',
        'is_active',
        'joined_team',
        'missed_password_times'
    ];
    const ROLE_ADMIN = 'admin';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function check_ins()  {
        return $this->hasMany(CheckIn::class);
    }

    public function vacation()  {
        return $this->hasOne(Vacation::class);
    }

    public function leaves()  {
        return $this->hasMany(Leave::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function personalHolidays() {
        return $this->hasMany(PersonalHoliday::class);
    }

    public function teams()  {
        return $this->hasMany(Team::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($user){
            if(empty($user->pin)){
                $user->pin = self::generateUniquePin();
            }
        });
    }

    public static function generateUniquePin(){
        do{
            $pin = mt_rand(1000, 9999);
        }while(self::where('pin', $pin)->exists());

        return $pin;
    }
    public function getFullNameAttribute()
    {
        return "{$this->surname} {$this->name}";
    }
    public function lastCheckins(): HasMany
    {
        return $this->hasMany(CheckIn::class, 'user_id')->where('date', '<=', now()->endOfDay())->orderBy('date', 'desc');
    }
}
