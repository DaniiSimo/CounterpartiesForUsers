<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Services\RegistrationUserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private readonly RegistrationUserService $registrationUserService)
    {
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $dto = $request->dto();

        $createdUser = $this->registrationUserService->registration(dto: $dto);

        return UserResource::make($createdUser)
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }
}
