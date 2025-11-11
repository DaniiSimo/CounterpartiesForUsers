<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Counterparty',
    type: 'object',
    properties: [
        new OA\Property(property: 'address', type: 'string', example: '117312, г Москва, Академический р-н, ул Вавилова, д 19'),
        new OA\Property(property: 'name', type: 'string', example: 'ПАО СБЕРБАНК'),
        new OA\Property(property: 'ogrn', type: 'string', maxLength: 15, example: '1027700132195', format: 'regex:/^(?:\d{13}|\d{15})$/'),
    ]
)]
class CounterpartyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'ogrn' => $this->ogrn
        ];
    }
}
