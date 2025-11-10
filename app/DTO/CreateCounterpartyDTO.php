<?php

namespace App\DTO;

use App\Models\User;

final readonly class CreateCounterpartyDTO
{
    public function __construct(public string $inn, public User $user)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            inn: $data['inn'],
            user: $data['user']
        );
    }
}
