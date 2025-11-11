<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Counterparty API', version: '1.0.0', contact: new OA\Contact(name: 'Данила', url: 'https://github.com/DaniiSimo'))]
#[OA\Server(
    url: '{scheme}://{host}',
    variables: [
        new OA\ServerVariable(serverVariable: 'scheme', default: 'http', enum: ['http','https']),
        new OA\ServerVariable(serverVariable: 'host', default: 'localhost'),
    ]
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Вводите токен без "Bearer " — UI подставит префикс сам'
)]
class OpenApi
{
}
