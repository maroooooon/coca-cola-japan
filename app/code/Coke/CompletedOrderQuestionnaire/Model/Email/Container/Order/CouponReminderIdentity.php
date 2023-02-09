<?php

namespace Coke\CompletedOrderQuestionnaire\Model\Email\Container\Order;

use Magento\Sales\Model\Order\Email\Container\Container;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;

class CouponReminderIdentity extends Container implements IdentityInterface
{
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/questionnaire_reminder/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/questionnaire_reminder/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/questionnaire_reminder/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'sales_email/questionnaire_reminder/template';

    /**
     * Is email enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        return false;
    }

    /**
     * Return copy method
     *
     * @return mixed
     */
    public function getCopyMethod()
    {
        return null;
    }

    /**
     * Return template id
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        return 'sales_email_coupon_reminder_template';
    }

    /**
     * Return email identity
     *
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }

    /**
     * @return mixed|null
     */
    public function getGuestTemplateId()
    {
        return null;
    }
}
