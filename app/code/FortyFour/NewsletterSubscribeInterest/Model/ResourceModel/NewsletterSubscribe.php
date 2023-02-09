<?php

namespace FortyFour\NewsletterSubscribeInterest\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class NewsletterSubscribe extends AbstractDb
{
    const TABLE_NAME = 'coke_newsletter_subscribe';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }
}
