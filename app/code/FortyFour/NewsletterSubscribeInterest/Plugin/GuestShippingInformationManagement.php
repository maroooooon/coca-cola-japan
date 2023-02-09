<?php

namespace FortyFour\NewsletterSubscribeInterest\Plugin;

use Magento\Checkout\Api\GuestShippingInformationManagementInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use FortyFour\NewsletterSubscribeInterest\Model\NewsletterSubscribeFactory;
use FortyFour\NewsletterSubscribeInterest\Model\ResourceModel\NewsletterSubscribe;
use Magento\Store\Model\StoreManagerInterface;

class GuestShippingInformationManagement {

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * Cart Repository
     *
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var NewsletterSubscribeFactory
     */
    private $newsletterSubscribeFactory;

    /**
     * @var NewsletterSubscribe
     */
    private $newsletterSubscribeResourceModel;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * GuestShippingInformationManagement constructor.
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param NewsletterSubscribeFactory $newsletterSubscribeFactory
     * @param NewsletterSubscribe $newsletterSubscribeResourceModel
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        NewsletterSubscribeFactory $newsletterSubscribeFactory,
        NewsletterSubscribe $newsletterSubscribeResourceModel,
        StoreManagerInterface $storeManager
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->newsletterSubscribeFactory = $newsletterSubscribeFactory;
        $this->newsletterSubscribeResourceModel = $newsletterSubscribeResourceModel;
        $this->_storeManager = $storeManager;
    }

    public function beforeSaveAddressInformation(
        GuestShippingInformationManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {

        try{
            $storeId = $this->_storeManager->getStore()->getId();

            $newsletterSubscribe = $this->newsletterSubscribeFactory->create();
            $newsletterSubscribe->setEmail($addressInformation->getExtensionAttributes()->getNewsletterSubscribe());
            $newsletterSubscribe->setStore($storeId);
            $this->newsletterSubscribeResourceModel->save($newsletterSubscribe);
        } catch (\Exception $e) {

        }
    }
}
