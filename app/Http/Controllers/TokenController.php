<?php

namespace App\Http\Controllers;

use App\DTO\ApiAuthenticationUserDTO;
use App\Http\Requests\Token\StoreRequest;
use App\Http\Resources\ApiTokenResource;
use App\Services\Api\AuthenticationUserService;
use Illuminate\Http\{Request, JsonResponse, Response};
use OpenApi\Attributes as OA;

class TokenController extends Controller
{
    public function __construct(private readonly AuthenticationUserService $authenticationUserService)
    {
    }

    #[OA\Post(
        path: '/api/tokens',
        tags: ['Token'],
        summary: 'Создание токена аутентификации api',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@test.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secretpassword'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            ref: '#/components/schemas/Token'
                        ),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Ошибка аутентификации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: "These credentials do not match our records"),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Ошибка валидации'),
                        new OA\Property(property: 'errors', type: 'object', example: ["email" => "Ошибка валидации",  "password" => "Ошибка валидации"]),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Превышен лимит попыток',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Too many login attempts. Please try again in n seconds.'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function store(StoreRequest $request): JsonResponse
    {
        $dataRequest = $request->safe()->only(['email', 'password']);

        $dto = ApiAuthenticationUserDTO::fromArray(data: array_merge($dataRequest, ['ip' => $request->ip()]));

        $apiToken = $this->authenticationUserService->authentication(dto: $dto);

        return ApiTokenResource::make((object)['token' => $apiToken])
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }
    #[OA\Delete(
        path: '/api/tokens',
        tags: ['Token'],
        security: [['bearerAuth' => []]],
        summary: 'Удаление токена аутентификации api, у аутентифицированного пользователя',
        responses: [
            new OA\Response(
                response: 204,
                description: 'No Content'
            ),
            new OA\Response(
                response: 400,
                description: 'Токена не существует',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: "Logout failed."),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function destroy(Request $request): Response | JsonResponse
    {
        $resultLogout = $this->authenticationUserService->logout(user: $request->user());

        return $resultLogout
            ? response()->noContent()
            : response()->json(data: ['message' => __(key: 'auth.logout_failed')], status: 400);
    }
}
