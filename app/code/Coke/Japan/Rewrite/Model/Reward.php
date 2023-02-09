<?php

namespace Coke\Japan\Rewrite\Model;

use Magento\Customer\Model\Customer;

class Reward extends \Magento\Reward\Model\Reward
{
    /**
     * Send low Balance Warning Notification to customer if notification is enabled
     *
     * @param object $item
     * @param int $websiteId
     * @return \Magento\Reward\Model\Reward
     * @see \Magento\Reward\Model\ResourceModel\Reward\History\Collection::loadExpiredSoonPoints()
     */
    public function sendBalanceWarningNotification($item, $websiteId)
    {
        $store = $this->_storeManager->getStore($item->getStoreId());

        if ($store->getWebsite()->getCode() != \Coke\Japan\Model\Website::MARCHE) {
            return parent::sendBalanceWarningNotification($item, $websiteId);
        }

        $helper = $this->_rewardData;
        $amount = $helper->getRateFromRatesArray(
            $item->getPointsBalanceTotal(),
            $websiteId,
            $item->getCustomerGroupId()
        );
        $action = $this->getActionInstance($item->getAction());

        $templateIdentifier = $this->_scopeConfig->getValue(
            self::XML_PATH_BALANCE_WARNING_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $from = $this->_scopeConfig->getValue(
            self::XML_PATH_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $remainingDays = $this->_scopeConfig->getValue(
            'magento_reward/notification/expiry_day_before',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        $this->_transportBuilder->setTemplateIdentifier(
            $templateIdentifier
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $item->getStoreId()]
        )->setTemplateVars(
            [
                'store' => $store,
                'customer_name' => $item->getCustomerLastname() . ' ' . $item->getCustomerFirstname(),
                'unsubscription_url' => $this->_rewardCustomer->getUnsubscribeUrl('warning'),
                'remaining_days' => $remainingDays,
                'points_balance' => $item->getPointsBalanceTotal(),
                'points_expiring' => $item->getTotalExpired(),
                'reward_amount_now' => $helper->formatAmount($amount, true, $item->getStoreId()),
                'update_message' => $action !== null ? $action->getHistoryMessage($item->getAdditionalData()) : '',
            ]
        )->setFrom(
            $from
        )->addTo(
            $item->getCustomerEmail()
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        return $this;
    }

    /**
     * Send Balance Update Notification to customer if notification is enabled
     *
     * @return \Magento\Reward\Model\Reward
     */
    public function sendBalanceUpdateNotification()
    {
        $store = $this->_storeManager->getStore($this->getStore());

        if ($store->getWebsite()->getCode() != \Coke\Japan\Model\Website::MARCHE) {
            return parent::sendBalanceUpdateNotification();
        }

        $customer = $this->getCustomer();
        // workaround for frontend and backend cases (they use different classes to represent customer)
        $notificationRequired = ($customer instanceof Customer)
            ? $customer->getData('reward_update_notification')
            : $this->getUpdateNotificationAttribute($customer);
        if (!$notificationRequired) {
            return $this;
        }
        $delta = (int)$this->getPointsDelta();
        if ($delta == 0) {
            return $this;
        }
        $history = $this->getHistory();

        $templateIdentifier = $this->_scopeConfig->getValue(
            self::XML_PATH_BALANCE_UPDATE_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $from = $this->_scopeConfig->getValue(
            self::XML_PATH_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        $this->_transportBuilder->setTemplateIdentifier(
            $templateIdentifier
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId()]
        )->setTemplateVars(
            [
                'store' => $store,
                'customer_name' => $customer->getLastname() . ' ' . $customer->getFirstname(),
                'unsubscription_url' => $this->_rewardCustomer->getUnsubscribeUrl('update', $store->getId()),
                'points_balance' => $this->getPointsBalance(),
                'reward_amount_was' => $this->_rewardData->formatAmount(
                    $this->getCurrencyAmount() - $history->getCurrencyDelta(),
                    true,
                    $store->getStoreId()
                ),
                'reward_amount_now' => $this->_rewardData->formatAmount(
                    $this->getCurrencyAmount(),
                    true,
                    $store->getStoreId()
                ),
                'reward_pts_was' => $this->getPointsBalance() - $delta,
                'reward_pts_change' => $delta,
                'update_message' => $this->getHistory()->getMessage(),
                'update_comment' => $history->getComment(),
            ]
        )->setFromByScope(
            $from,
            $store->getId()
        )->addTo(
            $this->getCustomer()->getEmail()
        );
        $transport = $this->_transportBuilder->getTransport();
        try {
            $transport->sendMessage();
            $this->setBalanceUpdateSent(true);
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (\Magento\Framework\Exception\MailException $e) {
        }
        return $this;
    }
}
