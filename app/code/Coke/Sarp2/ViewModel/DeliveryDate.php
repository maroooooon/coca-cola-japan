<?php

namespace Coke\Sarp2\ViewModel;

use Coke\Sarp2\Service\DeliveryDateCalculator;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class DeliveryDate implements ArgumentInterface
{
    /**
     * @var DeliveryDateCalculator
     */
    private $deliveryDateCalculator;

    /**
     * @param DeliveryDateCalculator $deliveryDateCalculator
     */
    public function __construct(
        DeliveryDateCalculator $deliveryDateCalculator
    ) {
        $this->deliveryDateCalculator = $deliveryDateCalculator;
    }

    /**
     * @return DeliveryDateCalculator
     */
    public function getDeliveryDateCalculator(): DeliveryDateCalculator
    {
        return $this->deliveryDateCalculator;
    }
}
