<?php

namespace App\Services\Api;

use App\DTO\{ApiAuthenticationUserDTO, ApiLogoutUserDTO};
use App\{Models\User, Services\RateLimiterService};
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

/**
 * Сервис аутентификации пользователя по api
 */
final readonly class AuthenticationUserService
{
    public function __construct(private RateLimiterService $rateLimiterService, private TokenService $apiTokenService)
    {
    }

    /**
     * Аутентификация
     *
     * @param ApiAuthenticationUserDTO $dto Данные для авторизации
     *
     * @return string Bearer token
     * @throws AuthenticationException Пользователь не найден или неправильный пароль
     */
    public function authentication(ApiAuthenticationUserDTO $dto): string
    {
        $rateLimiterKey = [$dto->email, $dto->ip];
        $this->rateLimiterService->ensureIsNotRateLimited(dataKey: $rateLimiterKey);

        $user = User::where(column: 'email', operator: '=', value: $dto->email)->first();

        if (
            is_null(value: $user)
            || !Hash::check(value: $dto->password, hashedValue: $user?->password)
        ) {
            $this->rateLimiterService->add(dataKey: $rateLimiterKey);
            throw new AuthenticationException(message:  __(key: 'auth.failed'));
        }

        $this->rateLimiterService->clear(dataKey: $rateLimiterKey);

        return $this->apiTokenService->create(user: $user);
    }

    /**
     * Выход
     *
     * @param ApiLogoutUserDTO $dto Данные пользователя
     *
     * @return bool
     */
    public function logout(ApiLogoutUserDTO $dto): bool
    {
        return $this->apiTokenService->delete(user: $dto->user);
    }
}
