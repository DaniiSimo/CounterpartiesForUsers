<?php

namespace App\Http\Controllers;

use App\DTO\ApiLogoutUserDTO;
use App\Http\Requests\Token\StoreRequest;
use App\Http\Resources\ApiTokenResource;
use App\Services\Api\AuthenticationUserService;
use Illuminate\Http\{Request, JsonResponse, Response};

class TokenController extends Controller
{
    public function __construct(private readonly AuthenticationUserService $authenticationUserService)
    {
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $dto = $request->dto();

        $apiToken = $this->authenticationUserService->authentication(dto: $dto);

        return ApiTokenResource::make((object)['token' => $apiToken])
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }

    public function destroy(Request $request): Response | JsonResponse
    {
        $dto = new ApiLogoutUserDTO(user: $request->user());

        $resultLogout = $this->authenticationUserService->logout(dto: $dto);

        return $resultLogout
            ? response()->noContent()
            : response()->json(data: ['message' => __(key: 'auth.logout_failed')], status: 400);
    }
}
