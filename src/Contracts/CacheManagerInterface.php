<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 22.05.2017
 * Time: 16:06
 */

namespace Errogaht\DeliveryAggregator\Contracts;


interface CacheManagerInterface
{
    public function get($key);

    public function delete($key);

    public function set($key, $value, $minutes);
}