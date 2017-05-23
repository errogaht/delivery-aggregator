<?php

return [
    'carriers' => [
        \Errogaht\DeliveryAggregator\Carrier\Pec::class => [
            'base_url' => 'https://kabinet.pecom.ru/api/v1',
            'login' => '',
            'key' => '',
        ],
        \Errogaht\DeliveryAggregator\Carrier\Dellin::class => [
            'base_url' => 'https://api.dellin.ru',
            'key' => '',
        ],
        \Errogaht\DeliveryAggregator\Carrier\Jde::class => [
            'base_url' => 'http://apitest.jde.ru:8000',
            'login' => '',
            'key' => '',
        ]
    ],
    'cache' => [
        'class' => \Errogaht\DeliveryAggregator\BuiltInCacheManagerManager::class,
        'default_cache_minutes' => 43200
    ]
];