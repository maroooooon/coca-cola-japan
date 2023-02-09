<?php

namespace CokeJapan\Sarp2\Model;

use Aheadworks\Sarp2\Model\Sales\Quote\Item\Option\SubscriptionOptions\Provider;
use Aheadworks\Sarp2\Api\SubscriptionOptionRepositoryInterface;
use Aheadworks\Sarp2\Model\Sales\Quote\Item\Option\SubscriptionOptions\ProviderInterface;
use Aheadworks\Sarp2\ViewModel\Subscription\Details\ForQuoteItem as QuoteItemDetailsViewModel;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomProvider extends Provider
{
    /**
     * @var SubscriptionOptionRepositoryInterface
     */
    private $optionRepository;

    /**
     * @var QuoteItemDetailsViewModel
     */
    private $detailsViewModel;

    /**
     * @param SubscriptionOptionRepositoryInterface $optionRepository
     * @param QuoteItemDetailsViewModel $itemDetailsViewModel
     */
    public function __construct(
        SubscriptionOptionRepositoryInterface $optionRepository,
        QuoteItemDetailsViewModel $itemDetailsViewModel
    ) {
        $this->optionRepository = $optionRepository;
        $this->detailsViewModel = $itemDetailsViewModel;
    }

    /**
     * Get detailed subscription options
     *
     * @param ItemInterface $item
     * @return array
     * @throws LocalizedException
     */
    public function getSubscriptionOptions(ItemInterface $item)
    {
        $details = [];
        if ($this->isSubscriptionItem($item)) {
            $addOption = function ($label, $value) use (&$details) {
                $details[] = [
                    'label' => $label,
                    'value' => $value
                ];
            };

            if ($this->detailsViewModel->isShowInitialDetails($item)) {
                $addOption(
                    $this->detailsViewModel->getInitialLabel(),
                    $this->detailsViewModel->getInitialLabel()
                );
            }
            if ($this->detailsViewModel->isShowTrialDetails($item)) {
                $addOption(
                    $this->detailsViewModel->getTrialLabel($item),
                    $this->detailsViewModel->getTrialLabel($item)
                );
            }
            if ($this->detailsViewModel->isShowRegularDetails($item)) {
                $addOption(
                    __('Delivery Cycle'),
                    $this->detailsViewModel->getRegularLabel($item)
                );
            }

            $addOption(
                __('Shipping Guideline'),
                __('Ships within 2~3 days')
            );

            $addOption(
                __('Time of payment'),
                __('Regular payment schedule')
            );

            $addOption(
                $this->detailsViewModel->getSubscriptionEndsDateLabel(),
                $this->detailsViewModel->getSubscriptionEndsDate($item)
            );
        }

        return array_values($details);
    }

    /**
     * Check if quote item has subscription option
     *
     * @param ItemInterface $item
     * @return bool
     */
    private function isSubscriptionItem($item)
    {
        $result = false;
        $itemOption = $item->getOptionByCode('aw_sarp2_subscription_type');
        if ($itemOption && $itemOption->getValue()) {
            try {
                $this->optionRepository->get($itemOption->getValue());
                $result = true;
            } catch (NoSuchEntityException $exception) {
                $result = false;
            }
        }

        return $result;
    }
}