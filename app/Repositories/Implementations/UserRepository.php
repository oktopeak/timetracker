<?php

namespace App\Repositories\Implementations;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserRepository extends Repository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
    public function getAllUsers(): Collection
    {
        return User::with('position')->get();
    }

    public function findByPin(string $pin): ?User
    {
        return User::where('pin', $pin)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function logout($user) {
        if($user) {
            $user->currentAccessToken()->delete();
            return true;
        }
        return false;
    }
    public function getUsersWithLastCheckin(): Collection
    {
        return User::with('lastCheckins')->get();
    }
}
