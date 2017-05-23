<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 18.05.2017
 * Time: 19:14
 */

namespace Errogaht\DeliveryAggregator\Entity\Unit;


class Length extends AbstractUnit
{

    /**
     * @return float
     */
    public function metres()
    {
        return (float)($this->value / 1000);
    }

    /**
     * @return int
     */
    public function millimetres()
    {
        return $this->value;
    }

}