<?php

namespace App\DTO;

final readonly class RegistrationUserDTO
{
    public function __construct(public string $email, public string $password, public ?string $name)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            name: $data['name'] ?? null
        );
    }
}
