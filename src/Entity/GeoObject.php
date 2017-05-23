<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 18.05.2017
 * Time: 12:30
 */

namespace Errogaht\DeliveryAggregator\Entity;


use Errogaht\DeliveryAggregator\Entity\Unit\Money;

class GeoObject
{
    /** @var string vendor id */
    protected $vid;

    /** @var Money */
    protected $use_cost;

    /** @var string */
    protected $lat;

    /** @var string */
    protected $lng;

    /** @var string */
    protected $name;

    /** @var string */
    protected $city_name;

    /** @var string */
    protected $address;

    /** @var string */
    protected $kladr;

    /** @var string */
    protected $fias;

    /** @var string */
    protected $region_name;

    /** @var string */
    protected $email;

    /** @var string */
    protected $phone;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return GeoObject
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return GeoObject
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getVid()
    {
        return $this->vid;
    }

    /**
     * @param string $vid
     * @return GeoObject
     */
    public function setVid($vid)
    {
        $this->vid = $vid;
        return $this;
    }

    /**
     * @return Money
     */
    public function getUseCost()
    {
        return $this->use_cost;
    }

    /**
     * @param float $use_cost
     * @return GeoObject
     */
    public function setUseCost($use_cost)
    {
        $this->use_cost = new Money($use_cost);
        return $this;
    }

    /**
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     * @return GeoObject
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param string $lng
     * @return GeoObject
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return GeoObject
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCityName()
    {
        return $this->city_name;
    }

    /**
     * @param string $city_name
     * @return GeoObject
     */
    public function setCityName($city_name)
    {
        $this->city_name = $city_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return GeoObject
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getKladr()
    {
        return $this->kladr;
    }

    /**
     * @param string $kladr
     * @return GeoObject
     */
    public function setKladr($kladr)
    {
        $this->kladr = $kladr;
        return $this;
    }

    /**
     * @return string
     */
    public function getFias()
    {
        return $this->fias;
    }

    /**
     * @param string $fias
     * @return GeoObject
     */
    public function setFias($fias)
    {
        $this->fias = $fias;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegionName()
    {
        return $this->region_name;
    }

    /**
     * @param string $region_name
     * @return GeoObject
     */
    public function setRegionName($region_name)
    {
        $this->region_name = $region_name;
        return $this;
    }


}