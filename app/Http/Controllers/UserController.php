<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\DTO\UserDTO;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\InvalidPasswordException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return response()->json($users);
    }

    public function store(UserStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $userDTO = UserDTO::from($request);
        $user = $this->userService->createUser($userDTO);

        return response()->json($user, 201);
    }

    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $user = $this->userService->getUser($id);

        if ($user) {
            return response()->json($user);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function update(UserUpdateRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        $userDTO = UserDTO::fromUpdateRequest($request);

        try {
            $user = $this->userService->updateUser($id, $request->input('old_password'), $userDTO);
            return response()->json($user);
        } catch (InvalidPasswordException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            return response()->json(['message' => 'User deleted successfully']);
        } catch (UserNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
