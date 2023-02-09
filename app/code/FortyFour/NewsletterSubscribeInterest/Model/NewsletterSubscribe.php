<?php

namespace FortyFour\NewsletterSubscribeInterest\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use FortyFour\NewsletterSubscribeInterest\Model\ResourceModel\NewsletterSubscribe as NewsletterSubscribeResourceModel;

class NewsletterSubscribe extends AbstractModel
{
    const TABLE_NAME = 'coke_newsletter_subscribe';

    const EMAIL = 'email';
    const STORE = 'store';

    /**
     * NewsletterSubscribe constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(NewsletterSubscribeResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function setStore($storeId)
    {
        return $this->setData(self::STORE, $storeId);
    }

}
