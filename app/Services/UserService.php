<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\DTO\UserDTO;
use App\Models\User;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userRepository->findAllUsers();
    }

    public function createUser(userDTO $userDTO): User
    {
        return $this->userRepository->createUser($userDTO);
    }

    public function updateUser(int $id, string $oldPassword, userDTO $userDTO): bool
    {
        return $this->userRepository->updateUser($id, $oldPassword, $userDTO);
    }

    public function getUser(int $id): ?User
    {
        return $this->userRepository->findUserById($id);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->deleteUser($id);
    }
}
