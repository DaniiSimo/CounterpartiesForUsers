<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Token',
    type: 'object',
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '1|fdsagfdasfdasfdsafdasf'),
    ]
)]
class ApiTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token
        ];
    }
}
