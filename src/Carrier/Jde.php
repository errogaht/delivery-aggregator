<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 13:26
 */

namespace Errogaht\DeliveryAggregator\Carrier;


use Errogaht\DeliveryAggregator\Carrier\AbstractCarrier;
use GuzzleHttp\Promise\PromiseInterface;

class Jde extends AbstractCarrier
{
    /**
     * @inheritdoc
     */
    public function getCalculateRequestPromise()
    {

    }
}