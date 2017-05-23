<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 17:59
 */

namespace Errogaht\DeliveryAggregator\Response;


use Errogaht\DeliveryAggregator\Carrier\AbstractCarrier;
use Errogaht\DeliveryAggregator\Entity\Unit\Money;
use Illuminate\Support\Collection;

class TransferOffer
{

    const TRANSFER_TYPE_AVIA = 'avia';
    const TRANSFER_TYPE_GROUND = 'ground';

    /** @var string */
    protected $rawCarrierResponse;

    /** @var string */
    protected $type;

    /** @var bool */
    protected $hasError = false;

    /** @var bool */
    protected $errorMessage = false;

    /** @var Money */
    protected $costTotal;

    /** @var string */
    protected $carrierCode;

    /** @var AbstractCarrier */
    protected $carrier;

    /** @var Collection */
    protected $sourceTerminals;

    /** @var Collection */
    protected $destinationTerminals;

    /** @var int */
    protected $minTerm;

    /** @var int */
    protected $maxTerm;

    public function __construct()
    {
        $this->destinationTerminals = Collection::make();
        $this->sourceTerminals = Collection::make();
    }

    /**
     * @return string
     */
    public function getRawCarrierResponse()
    {
        return $this->rawCarrierResponse;
    }

    /**
     * @param string $rawCarrierResponse
     * @return TransferOffer
     */
    public function setRawCarrierResponse($rawCarrierResponse)
    {
        $this->rawCarrierResponse = $rawCarrierResponse;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return TransferOffer
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param bool $errorMessage
     * @return TransferOffer
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @return Money
     */
    public function getCostTotal()
    {
        return $this->costTotal;
    }

    /**
     * @param Money $costTotal
     * @return TransferOffer
     */
    public function setCostTotal($costTotal)
    {
        $this->costTotal = $costTotal;
        return $this;
    }

    /**
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->carrierCode;
    }

    /**
     * @param string $carrierCode
     * @return TransferOffer
     */
    public function setCarrierCode($carrierCode)
    {
        $this->carrierCode = $carrierCode;
        return $this;
    }

    /**
     * @return AbstractCarrier
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param AbstractCarrier $carrier
     * @return TransferOffer
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getSourceTerminals()
    {
        return $this->sourceTerminals;
    }

    /**
     * @param Collection $sourceTerminals
     * @return TransferOffer
     */
    public function setSourceTerminals($sourceTerminals)
    {
        $this->sourceTerminals = $sourceTerminals;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getDestinationTerminals()
    {
        return $this->destinationTerminals;
    }

    /**
     * @param Collection $destinationTerminals
     * @return TransferOffer
     */
    public function setDestinationTerminals($destinationTerminals)
    {
        $this->destinationTerminals = $destinationTerminals;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinTerm()
    {
        return $this->minTerm;
    }

    /**
     * @param int $minTerm
     * @return TransferOffer
     */
    public function setMinTerm($minTerm)
    {
        $this->minTerm = $minTerm;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxTerm()
    {
        return $this->maxTerm;
    }

    /**
     * @param int $maxTerm
     * @return TransferOffer
     */
    public function setMaxTerm($maxTerm)
    {
        $this->maxTerm = $maxTerm;
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
     * @return TransferOffer
     */
    public function setHasError($hasError)
    {
        $this->hasError = $hasError;
        return $this;
    }


}