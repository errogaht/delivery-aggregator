<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 13:36
 */

namespace Errogaht\DeliveryAggregator;


use Errogaht\DeliveryAggregator\Carrier\AbstractCarrier;
use Errogaht\DeliveryAggregator\Contracts\CacheManagerInterface;
use Errogaht\DeliveryAggregator\Entity\Cargo;
use Errogaht\DeliveryAggregator\Entity\Transfer;
use Errogaht\DeliveryAggregator\Entity\TransferCalculationResponse;
use Errogaht\DeliveryAggregator\Response\TransferOffer;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Collection;

class ShippingManager
{

    public $config;
    /** @var array */
    public $carriers = [];

    /** @var Transfer */
    public $transfer;

    /** @var Cargo */
    private $cargo;

    /** @var Client */
    private $client;

    /** @var CacheManagerInterface */
    private $cacheManager;

    /**
     * ShippingManager constructor.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'timeout' => 5.0
        ]);
    }

    public function setTransfer(Transfer $transferRequest)
    {
        $this->transfer = $transferRequest;
    }

    public function setCargo(Cargo $cargo)
    {
        if ($cargo->countItems() === 0) {
            throw new \InvalidArgumentException('cargo must contains CargoItem\'s');
        }
        $this->cargo = $cargo;
    }

    public function addCarrier(AbstractCarrier $carrier)
    {
        if (is_null($carrier->getTransfer())) {
            $carrier->setTransfer($this->transfer);
        }

        if (is_null($carrier->getCargo())) {
            $carrier->setCargo($this->cargo);
        }

        $this->carriers[] = $carrier;
    }

    public function getCacheManager()
    {
        if (null === $this->cacheManager) {
            if (!isset($this->config['cache']['class']) || !class_exists($this->config['cache']['class'])) {
                throw new \Exception('cacheManager not configured');
            }
            $cl = $this->config['cache']['class'];
            $this->cacheManager = new $cl;
        }
        return $this->cacheManager;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function calculate()
    {
        if (count($this->carriers) < 1) {
            throw new \Exception('Unable calculate with 0 carriers');
        }
        $transferOffers = new Collection();

        $calculatePromises = [];
        $carrierTypeMap = [];

        foreach ($this->carriers as $index => $carrier) {
            $class = get_class($carrier);
            try {
                $carrier->setClient($this->getClient());
                $carrierTypeMap[$index . $class] = $carrier;
                if (!isset($this->config['carriers'][$class])) {
                    throw new \Exception('No config provided for ' . $class);
                }
                $carrier->setConfig($this->config['carriers'][get_class($carrier)])->setShippingManager($this);

                $calculatePromises[$index . $class] = $carrier->getCalculateRequestPromise();
            } catch (\Exception $e) {
                $transferOffers->push(
                    (new TransferOffer())
                        ->setHasError(true)
                        ->setCarrierCode($carrier->getCarrierCode())
                        ->setErrorMessage(get_class($e) . ": {$e->getMessage()}")
                );
            }
        }


        $results = \GuzzleHttp\Promise\settle($calculatePromises)->wait();
        foreach ($results as $carrierKey => $result) {
            if ($result['state'] === PromiseInterface::FULFILLED) {
                if (!method_exists($carrierTypeMap[$carrierKey], 'parseCalculateResponse')) {
                    throw new \Exception('parseCalculateResponse not found');
                }
                /** @var TransferCalculationResponse $calculationResponse */
                $calculationResponse = $carrierTypeMap[$carrierKey]->parseCalculateResponse($result['value']);

                if (!$calculationResponse->isHasError()) {
                    $calculationResponse->getOffers()->each(function (TransferOffer $transferOffer) use ($transferOffers
                    ) {
                        $transferOffers->push($transferOffer);
                    });
                }

            }
        }
        return $transferOffers;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     * @return ShippingManager
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }


}