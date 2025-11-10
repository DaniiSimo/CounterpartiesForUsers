<?php

namespace App\Http\Controllers;

use App\DTO\CreateCounterpartyDTO;
use App\Http\Requests\Counterparty\StoreRequest;
use App\Http\Resources\CounterpartyResource;
use App\Services\CounterpartyService;
use Illuminate\Http\{Request, JsonResponse};

class CounterpartyController extends Controller
{
    public function __construct(private readonly CounterpartyService $counterpartyService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        return CounterpartyResource::collection($request->user()->counterparties)
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }

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
