<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\DTO\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\InvalidPasswordException;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userRepository->findAll();
    }

    public function getUser(int $id): ?User
    {
        $user = $this->userRepository->findById($id);

        if ( ! $user) {
            return null;
        }

        return $user;
    }

    public function createUser(userDTO $userDTO): User
    {
        $userData = $userDTO->toArray();
        return $this->userRepository->create($userData);
    }

    /**
     * @throws InvalidPasswordException
     * @throws UserNotFoundException
     */
    public function updateUser(int $id, string $oldPassword, userDTO $userDTO): User
    {
        $user = $this->userRepository->findById($id);

        if ( ! $user) {
            throw new UserNotFoundException("User not found");
        }

        if ( ! Hash::check($oldPassword, $user->password)) {
            throw new InvalidPasswordException("Invalid password");
        }

        $updateData = collect($userDTO->toArray())
            ->reject(fn($value) => is_null($value))
            ->toArray();

        $this->userRepository->update($id, $updateData);

        return $this->userRepository->findById($id);
    }

    /**
     * @throws UserNotFoundException
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->findById($id);

        if ( ! $user) {
            throw new UserNotFoundException("User not found");
        }

        return $this->userRepository->delete($id);
    }
}
