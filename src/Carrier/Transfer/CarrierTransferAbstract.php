<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 16:18
 */

namespace Errogaht\DeliveryAggregator\Carrier\Transfer;


use Errogaht\DeliveryAggregator\Carrier\AbstractCarrier;

class CarrierTransferAbstract
{
    /** @var AbstractCarrier */
    protected $carrier;

    /** @var string id точки забора в системе перевозчика */
    public $from;

    /** @var string id точки доставки в системе перевозчика */
    public $to;

    public function __construct(AbstractCarrier $carrier)
    {
        $this->carrier = $carrier;
    }
}