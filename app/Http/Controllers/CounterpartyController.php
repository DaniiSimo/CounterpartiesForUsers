<?php

namespace App\Http\Controllers;

use App\DTO\CreateCounterpartyDTO;
use App\Http\Requests\Counterparty\StoreRequest;
use App\Http\Resources\CounterpartyResource;
use App\Services\CounterpartyService;
use Illuminate\Http\{Request, JsonResponse};
use OpenApi\Attributes as OA;

class CounterpartyController extends Controller
{
    public function __construct(private readonly CounterpartyService $counterpartyService)
    {
    }
    #[OA\Get(
        path: '/api/counterparties',
        tags: ['Counterparty'],
        security: [['bearerAuth' => []]],
        summary: 'Получения списка контрагентов авторизированного пользователя',
        parameters: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Counterparty')
                        ),
                    ],
                    type: 'object'
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
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        return CounterpartyResource::collection($request->user()->counterparties)
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }
    #[OA\Post(
        path: '/api/counterparties',
        tags: ['Counterparty'],
        security: [['bearerAuth' => []]],
        summary: 'Создание контрагента',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['inn'],
                properties: [
                    new OA\Property(property: 'inn', type: 'string', example: '7707083893', format: 'regex:/^(?:\d{10}|\d{12})$/'),
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
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Counterparty')
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
                        new OA\Property(property: 'errors', type: 'object', example: ["inn" => "Ошибка валидации"]),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Ошибка уникальности',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Counterparty conflict (duplicate OGRN)'),
                        new OA\Property(
                            property: 'ogrns',
                            type: 'array',
                            items: new OA\Items(type: 'string'),
                            example: ['1027700132195','1027700132196']
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function store(StoreRequest $request): JsonResponse
    {
        $dataRequest = $request->safe()->only('inn');

        $dto = new CreateCounterpartyDTO(
            inn:$dataRequest['inn'],
            user:$request->user()
        );

        $result = $this->counterpartyService->create(dto: $dto);

        return CounterpartyResource::collection($result)
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }
}
