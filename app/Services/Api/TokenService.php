<?php

namespace App\Services\Api;

use App\Models\User;

/**
 * Сервис генерации токенов авторизации api
 */
final class TokenService
{
    /** @const string Префикс для создания токенов авторизации по api */
    public const string PREFIX = 'api';

    /**
     * Генерация токенов
     *
     * @param User $user Пользователь, для которого создаётся токен
     *
     * @return string Bearer token
     */
    public function create(User $user): string
    {
        $this->delete(user: $user);
        return $user->createToken(name:self::PREFIX)->plainTextToken;
    }

    /**
     * Удаление токена
     *
     * @param User $user Пользователь, для которого удаляется токен
     *
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->tokens()->where(column:'name', operator: '=', value: self::PREFIX)->delete() !== 0;
    }
}
