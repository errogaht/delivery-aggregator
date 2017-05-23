<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 22.05.2017
 * Time: 16:06
 */

namespace Errogaht\DeliveryAggregator;


use Errogaht\DeliveryAggregator\Contracts\CacheManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class BuiltInCacheManagerManager implements CacheManagerInterface
{
    private $adapter;

    public function __construct()
    {
        $this->adapter = new FilesystemAdapter();
    }

    public function delete($key)
    {
        $this->adapter->deleteItem($key);
    }


    public function get($key)
    {
        $cacheItem = $this->adapter->getItem($key);
        return $cacheItem->get();
    }

    public function set($key, $value, $minutes)
    {
        $cacheItem = $this->adapter->getItem($key);
        $cacheItem->expiresAfter($minutes * 60);
        $cacheItem->set($value);
        $this->adapter->save($cacheItem);
    }

}