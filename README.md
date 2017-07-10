# pecom-public-api
Сделано в стенах [Broccoli-dev](https://brcl.ru/)  

Агрегатор служб доставки, подходит когда нужно возить грузы большие
ПЭК желдор Дел линии 

пока первая версия которую можно хоть как-то использовать

`composer require errogaht/delivery-aggregator`

работает пока ТОЛЬКО ПЭК, Дел линии 

в example_usage.php показано как работать

```php
use Errogaht\DeliveryAggregator\Carrier\Dellin;
use Errogaht\DeliveryAggregator\Carrier\Jde;
use Errogaht\DeliveryAggregator\Carrier\Pec;
use Errogaht\DeliveryAggregator\Entity\Cargo;
use Errogaht\DeliveryAggregator\Entity\CargoItem;
use Errogaht\DeliveryAggregator\Entity\Transfer;
use Errogaht\DeliveryAggregator\ShippingManager;

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

```
