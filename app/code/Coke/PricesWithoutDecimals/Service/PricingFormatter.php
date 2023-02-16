<?php

namespace Coke\PricesWithoutDecimals\Service;

class PricingFormatter
{
    public function floorCents($price)
    {
        // Rounding is handled by a static framework method which cannot be overwritten,
        // therefore we trick the rounding method into always rounding down by making the cents 00.
        if (!$price) {
            return 0;
        }
        $amountParts = explode(".", $price);
        $preFormattedAmount = $amountParts[0] . "." . 00;

        return (float)$preFormattedAmount;
    }

}
