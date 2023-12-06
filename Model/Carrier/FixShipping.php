<?php

namespace ModMage\Ship\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use ModMage\Ship\Model\ResourceModel\Ways\CollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\CartFactory;

class FixShipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_code = 'MegaShipping';
    protected $shipwaysFactory;
    protected $cartFactory;
    protected $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory         $rateErrorFactory,
        LoggerInterface      $logger,
        ResultFactory        $rateResultFactory,
        MethodFactory        $rateMethodFactory,
        CollectionFactory    $shipwaysFactory,
        CartFactory          $cartFactory,
        array                $data = []
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->shipwaysFactory = $shipwaysFactory;
        $this->cartFactory = $cartFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }


    public function getAllowedMethods()
    {
        return ['MegaShipping' => $this->getConfigData('name')];
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $cart = $this->cartFactory->create();
        $subTotal = $cart->getQuote()->getSubtotal();
        $items = $cart->getQuote()->getAllItems();

        $weight = 0;
        foreach ($items as $item) {
            $weight += ($item->getWeight() * $item->getQty());
        }

        $result = $this->_rateResultFactory->create();
        $collection = $this->shipwaysFactory->create();

        foreach ($collection as $coll) {
            $min_subtotal = $coll->getMinSubtotal();
            $max_subtotal = $coll->getMaxSubtotal();
            $min_weight = $coll->getMinWeight();
            $max_weight = $coll->getMaxWeight();
            $status = $coll->getStatus();

            if ($min_subtotal <= $subTotal && $subTotal <= $max_subtotal &&
                $min_weight <= $weight && $weight <= $max_weight &&
                $status == 1
            ) {
                try {
                    $percent = $coll->getPercent();
                    $method = $this->_rateMethodFactory->create();
                    $method->setCarrier($this->_code);
                    $method->setCarrierTitle('MegaShipping');
                    $method->setMethod($coll->getId());
                    $method->setMethodTitle($coll->getName());
                    $amount = $coll->getPrice();
                    $method->setPrice($amount + $amount * $percent / 100);
                    $method->setCost($amount + $amount * $percent / 100);

                    $result->append($method);
                } catch (\Exception $e) {
                    $this->logger->error($e);
                }
            }
        }
        return $result;
    }
}
