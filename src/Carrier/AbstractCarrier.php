<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 13:17
 */

namespace Errogaht\DeliveryAggregator\Carrier;


use Errogaht\DeliveryAggregator\Carrier\Transfer\CarrierTransferAbstract;
use Errogaht\DeliveryAggregator\Entity\Cargo;
use Errogaht\DeliveryAggregator\Entity\Transfer;
use Errogaht\DeliveryAggregator\Entity\TransferCalculationResponse;
use Errogaht\DeliveryAggregator\ShippingManager;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractCarrier
{
    protected $code;

    protected $config;

    /** @var Transfer */
    protected $transfer;

    /** @var CarrierTransferAbstract */
    protected $carrierTransfer;

    /** @var Cargo */
    protected $cargo;

    /** @var  Client */
    protected $client;

    /** @var ShippingManager */
    protected $shippingManager;

    /**
     * @return ShippingManager
     */
    public function getShippingManager()
    {
        return $this->shippingManager;
    }

    /**
     * @param ShippingManager $shippingManager
     * @return AbstractCarrier
     */
    public function setShippingManager($shippingManager)
    {
        $this->shippingManager = $shippingManager;
        return $this;
    }


    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return AbstractCarrier
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param Client $client
     * @return AbstractCarrier
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }


    /**
     * @return Transfer
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * @param Transfer $transferRequest
     * @return AbstractCarrier
     */
    public function setTransfer(Transfer $transferRequest)
    {
        $this->transfer = $transferRequest;
        return $this;
    }

    /**
     * @return Cargo
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * @param Cargo $package
     * @return AbstractCarrier
     */
    public function setCargo(Cargo $package)
    {
        $this->cargo = $package;
        return $this;
    }


    public function setCode($code)
    {
        if (!is_scalar($code)) {
            throw new \InvalidArgumentException('Only scalar types allowed');
        }
        $this->code = $code;
    }

    /** @return PromiseInterface */
    abstract public function getCalculateRequestPromise();

    /**
     * Возвращает ID населённого пункта из бд перевозчика
     * в функцию передаём полное название города откуда нужно везти
     * @param $cityName
     * @return mixed
     */
    abstract public function getCarrierCityId($cityName);

    public function __construct(array $config = null)
    {
        $this->config = $config;
        $this->code = $this->getCarrierCode();
        $this->carrierTransfer = $this->getCarrierTransfer();
    }

    /** @return bool */
    abstract protected function isPackageOversized();

    abstract public function getCarrierTransfer();

    abstract public function getCarrierCode();

    /** @return TransferCalculationResponse */
    abstract public function parseCalculateResponse(ResponseInterface $response);
}