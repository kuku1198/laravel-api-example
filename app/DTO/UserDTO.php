<?php

namespace App\DTO;

class UserDTO
{
    private ?string $name;
    private ?string $email;
    private ?string $password;

    public function __construct(
        ?string $name = null,
        ?string $email = null,
        ?string $password = null,
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
