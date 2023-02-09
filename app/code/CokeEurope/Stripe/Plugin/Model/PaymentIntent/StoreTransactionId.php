<?php

namespace CokeEurope\Stripe\Plugin\Model\PaymentIntent;

use Magento\Framework\Exception\PaymentException;

/**
 * @see \StripeIntegration\Payments\Model\PaymentIntent
 */
class StoreTransactionId
{
    /**
     * @param $subject
     * @param $result
     * @param $order
     * @return mixed
     * @throws PaymentException
     */
    public function afterConfirmAndAssociateWithOrder($subject, $result, $order)
    {
        $paymentIntent = $result; //Payment Intent
        $payment = $order->getPayment();

        $chargeBalanceTransaction = $this->getChargeBalanceTransactionId($paymentIntent);
        $payment->setTransactionAdditionalInfo('charge_transaction_id', $chargeBalanceTransaction);

        return $result;
    }

    /**
     * @param \Stripe\PaymentIntent $paymentIntent
     * @return string|\Stripe\BalanceTransaction|null
     * @throws PaymentException
     */
    protected function getChargeBalanceTransactionId(\Stripe\PaymentIntent $paymentIntent)
    {
        /** @var \Stripe\Collection $charges */
        $charges = $paymentIntent->charges;

        if ($charges->total_count === 0) {
            throw new PaymentException(__('Could not find a successful capture. There is no transaction_id available for this payment.'));
        }

        /** @var \Stripe\Charge $charge */
        $charge = $charges->data[0];

        // This HAS to exist, otherwise we'll have a problem.
        return $charge->balance_transaction;
    }
}
