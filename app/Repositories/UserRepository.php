<?php

namespace App\Repositories;

use App\DTO\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function findAllUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::all();
    }

    public function findUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function createUser(UserDTO $userDTO): User
    {
        $user = User::create([
            'name' => $userDTO->getName(),
            'email' => $userDTO->getEmail(),
            'password' => $userDTO->getPassword()
        ]);

        return $user;
    }

    public function updateUser(int $id, string $oldPassword, UserDTO $userDTO): bool
    {
        $user = User::find($id);

        if ( ! $user) {
            return false;
        }

        if ( ! Hash::check($oldPassword, $user->password)) {
            throw new \InvalidArgumentException("Invalid Password", 400);
        }

        return $user->update(array_filter([
            'name' => $userDTO->getName(),
            'email' => $userDTO->getEmail(),
            'password' => $userDTO->getPassword(),
        ], fn($value) => ! is_null($value)));
    }

    public function deleteUser(int $id): bool
    {
        $user = User::find($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }
}
