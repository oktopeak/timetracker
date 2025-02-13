<?php

namespace App\Services;

use App\Repositories\Implementations\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function __construct(private readonly UserRepository $userRepo) {}

    public function getAllUsers() {
        return $this->userRepo->getAllUsers();
    }

    public function login($email, $password) {
        $user = $this->userRepo->findByEmail($email);

        if($user && $user->missed_password_times >= 5) {
            throw new \Exception('Your account is locked! You have failed to login ' . $user->missed_password_times . ' times.');
        }else if ($user && !Hash::check($password, $user->password)) {
            $timesTried = $user->missed_password_times + 1;
            $this->userRepo->update($user, ['missed_password_times' => $timesTried]);
            throw new \Exception('Invalid password!');
        }else if(!$user){
            throw new \Exception('Invalid email!');
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'message' => 'Login successful',
            'token' => $token,
            'id' => $user->id,
            'firstName' => $user->name,
            'lastName' => $user->surname,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }

    public function logout($user) {
        return $this->userRepo->logout($user);
    }

    public function getUser($userId)
    {
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            throw new \Exception('User not found.', 404);
        }

        return $user;
    }

    public function createUser($data)
    {
        $data['role'] = 'employee';
        $data['password'] = Hash::make($data['password']);

        return $this->userRepo->create($data);
    }

    public function updateuser($id, $data)
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new \Exception('User not found.', 404);
        }

        return $this->userRepo->update($user, $data);
    }

    public function destroy($id)
    {
        $user = $this->userRepo->findById($id);

        if (!$user) {
            throw new \Exception('User not found.', 404);
        }

        return $this->userRepo->destroy($user);
    }
}
