<?php

namespace App\Http\Controllers;

use App\DTO\ApiAuthenticationUserDTO;
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
        $dataRequest = $request->safe()->only(['email', 'password']);

        $dto = ApiAuthenticationUserDTO::fromArray(data: array_merge($dataRequest, ['ip' => $request->ip()]));

        $apiToken = $this->authenticationUserService->authentication(dto: $dto);

        return ApiTokenResource::make((object)['token' => $apiToken])
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }

    public function destroy(Request $request): Response | JsonResponse
    {
        $resultLogout = $this->authenticationUserService->logout(user: $request->user());

        return $resultLogout
            ? response()->noContent()
            : response()->json(data: ['message' => __(key: 'auth.logout_failed')], status: 400);
    }
}
