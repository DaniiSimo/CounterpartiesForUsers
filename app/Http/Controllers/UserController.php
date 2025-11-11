<?php

namespace App\Http\Controllers;

use App\DTO\RegistrationUserDTO;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Services\RegistrationUserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    public function __construct(private readonly RegistrationUserService $registrationUserService)
    {
    }
    #[OA\Post(
        path: '/api/users',
        tags: ['User'],
        summary: 'Регистрация',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email','password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'test'),
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
                            ref: '#/components/schemas/User'
                        ),
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
                        new OA\Property(property: 'errors', type: 'object', example: ["name" => "Ошибка валидации","email" => "Ошибка валидации",  "password" => "Ошибка валидации"]),
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function store(StoreRequest $request): JsonResponse
    {
        $dataRequest = $request->safe()->only(['name', 'email', 'password']);

        $dto = RegistrationUserDTO::fromArray(data: $dataRequest);

        $createdUser = $this->registrationUserService->registration(dto: $dto);

        return UserResource::make($createdUser)
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }
}
