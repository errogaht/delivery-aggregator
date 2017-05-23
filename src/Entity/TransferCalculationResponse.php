<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 22.05.2017
 * Time: 13:17
 */

namespace Errogaht\DeliveryAggregator\Entity;


use Illuminate\Support\Collection;

class TransferCalculationResponse
{
    /** @var Collection */
    protected $offers;

    /** @var bool */
    protected $hasError = false;

    /** @var mixed */
    protected $errorData;

    /**
     * @return Collection
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @param Collection $offers
     * @return TransferCalculationResponse
     */
    public function setOffers($offers)
    {
        $this->offers = $offers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasError()
    {
        return $this->hasError;
    }

    /**
     * @param bool $hasError
     * @return TransferCalculationResponse
     */
    public function setHasError($hasError)
    {
        $this->hasError = $hasError;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrorData()
    {
        return $this->errorData;
    }

    /**
     * @param mixed $errorData
     * @return TransferCalculationResponse
     */
    public function setErrorData($errorData)
    {
        $this->errorData = $errorData;
        return $this;
    }


}