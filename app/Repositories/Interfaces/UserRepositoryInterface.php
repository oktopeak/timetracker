<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getAllUsers();
    public function logout($user);
    public function findByPin(string $pin);
    public function findByEmail(string $email);
    public function getUsersWithLastCheckin(): Collection;
}
