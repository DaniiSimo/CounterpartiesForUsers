<?php

return [
    'dadata' => [
        'token' => env(key: 'DADATA_TOKEN'),
        'urls' => [
            'organization' => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party'
        ],
        'timeout'  => 5,
        'connect_timeout' => 3,
        'retry' => [
            'times' => 3,
            'sleep_ms' => 200,
        ],
        'log_channel' => 'external_api_dadata',
    ]

];
