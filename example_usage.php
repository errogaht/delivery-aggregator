<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Errogaht\DeliveryAggregator\Carrier\Dellin;
use Errogaht\DeliveryAggregator\Carrier\Jde;
use Errogaht\DeliveryAggregator\Carrier\Pec;
use Errogaht\DeliveryAggregator\Entity\Cargo;
use Errogaht\DeliveryAggregator\Entity\CargoItem;
use Errogaht\DeliveryAggregator\Entity\Transfer;
use Errogaht\DeliveryAggregator\ShippingManager;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * pickupPoint
 * pickup - забор груза от двери
 *
 * SourceTerminal
 * DestinationTerminal
 *
 * toPoint
 *
 * delivery - доставка до двери
 *
 * deliveryPoint
 */

$config = require __DIR__ . '/config.php';

$cargo = new Cargo();
$item = new CargoItem();
$item->setHeight(250)->setLength(252)->setWidth(252)->setWeight(15000)->setPrice(1000);
$cargo->addItem($item);
$cargo->addItem($item);


$transfer = new Transfer();
$transfer
    ->setIsInsurance(true)
    ->setIsDelivery(false)
    ->setCityFrom('Москва')
    ->setCityTo('Владивосток')
    ->setIsPickup(false);

$manager = new ShippingManager($config);
$manager->setCargo($cargo);
$manager->setTransfer($transfer);


$carrier = new Pec();
$carrierTransfer = $carrier->getCarrierTransfer();
$carrierTransfer->cargoIsHardPack = true;
$manager->addCarrier($carrier);


$carrier = new Dellin();
$carrierTransfer = $carrier->getCarrierTransfer();
$carrierTransfer->cargoIsHardPack = true;
$carrierTransfer->from = '7800000000000000000000000';
$manager->addCarrier($carrier);

$offers = $manager->calculate();
d($offers);

/*
$base_url = 'https://api.dellin.ru';
$password = '';

$params = [
    'appKey' => $password,
    'derivalPoint' => '7800000000000000000000000',
    'arrivalPoint' => '5200000100000000000000000',
    'derivalDoor' => $transfer->isPickup(),
    'arrivalDoor' => $transfer->isDelivery(),
    'sizedVolume' => $cargo->getVolume()->cubicMetres(),
    'sizedWeight' => $cargo->getWeight()->kilograms(),
    'oversizedVolume' => 0,
    'oversizedWeight' => 0,
    'length' => $cargo->getLength()->metres(),
    'width' => $cargo->getWidth()->metres(),
    'height' => $cargo->getHeight()->metres(),
    'maxWeight' => $cargo->getMaxWeight()->kilograms(),
    'statedValue' => $transfer->isInsurance() ? $cargo->getCost() : 0,
    'quantity' => 1,
    'packages' => [
        '0xAD22189D098FB9B84EEC0043196370D6'
    ],
    'derivalServices' => [
        '0xb83b7589658a3851440a853325d1bf69'
    ],
    'arrivalServices' => [
        '0xb83b7589658a3851440a853325d1bf69'
    ]
    ,
    'derivalLoading' => [
        [
            'uid' => '0xa77fcf6a449164ed490133777a68bd51'
        ],
        [
            'uid' => '0xadf1fc002cb8a9954298677b22dbde12',
            'value' => '4'
        ],
        [
            'uid' => '0x9a0d647ddb11ebbd4ddaaf3b1d9f7b74',
            'value' => '58'
        ]
    ],
    'arrivalUnloading' => [
        [
            'uid' => '0xa77fcf6a449164ed490133777a68bd51'
        ],
        [
            'uid' => '0xadf1fc002cb8a9954298677b22dbde12',
            'value' => '4'
        ],
        [
            'uid' => '0x9a0d647ddb11ebbd4ddaaf3b1d9f7b74',
            'value' => '58'
        ]
    ],
];

$promises[] = $client->requestAsync('POST', "$base_url/v1/public/calculator.json", [
    RequestOptions::HEADERS => [
        'Content-Type' => 'application/json;charset=utf-8',
        'Accept' => 'application/json',
        'Accept-Encoding' => 'gzip'
    ],
    //RequestOptions::AUTH => [$login, $password],
    RequestOptions::JSON => $params,
    RequestOptions::VERIFY => false
]);




$base_url = 'http://apitest.jde.ru:8000';
$password = '';

$params = [
    'from' => '1125899906842658',
    'to' => '1125899906842673',
    'weight' => $cargo->getWeight()->kilograms(),
    'volume' => $cargo->getVolume()->cubicMetres(),
    'length' => $cargo->getLength()->metres(),
    'width' => $cargo->getWidth()->metres(),
    'height' => $cargo->getHeight()->metres(),
    'quantity' => 1,
    'pickup' => $transfer->isPickup(),
    'delivery' => $transfer->isDelivery(),
    'declared' => $transfer->isInsurance() ? $cargo->getCost() : 0,
    'services' => '',
    'oversizeWeight' => 0,
    'oversizeVolume' => 0,
    'obrVolume' => 0,

];
$promises[] = $client->requestAsync('GET', "$base_url/calculator/price", [
    RequestOptions::HEADERS => [
        'Content-Type' => 'application/json;charset=utf-8',
        'Accept' => 'application/json',
        'Accept-Encoding' => 'gzip'
    ],
    //RequestOptions::AUTH => [$login, $password],
    RequestOptions::QUERY => $params
]);*/




