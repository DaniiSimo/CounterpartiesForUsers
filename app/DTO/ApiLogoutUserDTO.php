<?php

namespace App\DTO;

use App\Models\User;

final readonly class ApiLogoutUserDTO
{
    public function __construct(public User $user)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            user: $data['user']
        );
    }
}
