<?php

namespace App\DTO;

use Spatie\LaravelData\Data;
use App\Http\Requests\UserUpdateRequest;

class UserDTO extends Data
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
    ) {
    }

    public static function fromUpdateRequest(UserUpdateRequest $request): self
    {
        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('new_password')
        ];

        return self::from($data);
    }
}
