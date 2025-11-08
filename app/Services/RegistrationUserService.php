<?php

namespace App\Services;

use App\{DTO\RegistrationUserDTO, Models\User};

/**
 * Сервис регистрации пользователя
 */
final class RegistrationUserService
{
    /**
     * Регистрация пользователя
     *
     * @param RegistrationUserDTO $dto Данные для создания пользователя
     *
     * @return User Созданный пользователь
     */
    public function registration(RegistrationUserDTO $dto): User
    {
        return User::create([
            'email' => $dto->email,
            'password' => $dto->password,
            'name' => $dto->name
        ]);
    }
}
