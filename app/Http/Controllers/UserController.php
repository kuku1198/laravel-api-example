<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\DTO\UserDTO;
use App\Http\Requests\UserStoreRequest;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();

        return response()->json($users);
    }

    public function store(UserStoreRequest $request)
    {
        $userDTO = new UserDTO(
            $request->input('name'),
            $request->input('email'),
            $request->input('password')
        );

        $user = $this->userService->createUser($userDTO);

        return response()->json($user, 201);
    }

    public function show(int $id)
    {
        $user = $this->userService->getUser($id);

        if ($user) {
            return response()->json($user);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
            'new_password' => ['nullable', Password::min(8)],
            'old_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $userDTO = new UserDTO(
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['new_password'],
        );

        $updated = $this->userService->updateUser($id, $data['old_password'], $userDTO);

        if ($updated) {
            return response()->json(['message' => 'User updated successfully']);
        }

        return response()->json(['message' => 'User not found or update failed'], 404);
    }

    public function destroy(int $id)
    {
        $deleted = $this->userService->deleteUser($id);

        if ($deleted) {
            return response()->json(['message' => 'User deleted successfully']);
        }

        return response()->json(['message' => 'User not found or delete failed'], 404);
    }
}
