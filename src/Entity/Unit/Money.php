<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 18.05.2017
 * Time: 19:14
 */

namespace Errogaht\DeliveryAggregator\Entity\Unit;


class Money extends AbstractUnit
{
    public function __construct($val)
    {
        $this->value = (float)$val;
    }

    public function roubles()
    {
        return $this->getValue();
    }
}