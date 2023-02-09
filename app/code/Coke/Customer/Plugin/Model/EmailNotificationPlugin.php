<?php

namespace Coke\Customer\Plugin\Model;

class EmailNotificationPlugin
{
    /**
     * Constants for the type of new account email to be sent
     */
    const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';

    /**
     * Welcome email, when password setting is required
     */
    const NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD = 'registered_no_password';

    /**
     * Welcome email, when confirmation is enabled
     */
    const NEW_ACCOUNT_EMAIL_CONFIRMATION = 'confirmation';

    /**
     * Confirmation email, when account is confirmed
     */
    const NEW_ACCOUNT_EMAIL_CONFIRMED = 'confirmed';

    const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'customer/create_account/email_template';

    const XML_PATH_REGISTER_NO_PASSWORD_EMAIL_TEMPLATE = 'customer/create_account/email_no_password_template';

    const XML_PATH_CONFIRM_EMAIL_TEMPLATE = 'customer/create_account/email_confirmation_template';

    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE = 'customer/create_account/email_confirmed_template';

    /**
     * self::NEW_ACCOUNT_EMAIL_REGISTERED               welcome email, when confirmation is disabled
     *                                                  and password is set
     * self::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD   welcome email, when confirmation is disabled
     *                                                  and password is not set
     * self::NEW_ACCOUNT_EMAIL_CONFIRMED                welcome email, when confirmation is enabled
     *                                                  and password is set
     * self::NEW_ACCOUNT_EMAIL_CONFIRMATION             email with confirmation link
     */
    const TEMPLATE_TYPES = [
        self::NEW_ACCOUNT_EMAIL_REGISTERED => self::XML_PATH_REGISTER_EMAIL_TEMPLATE,
        self::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD => self::XML_PATH_REGISTER_NO_PASSWORD_EMAIL_TEMPLATE,
        self::NEW_ACCOUNT_EMAIL_CONFIRMED => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE,
        self::NEW_ACCOUNT_EMAIL_CONFIRMATION => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE,
    ];

    public function beforeNewAccount(\Magento\Customer\Model\EmailNotification $subject, \Magento\Customer\Api\Data\CustomerInterface $customer, $type = self::NEW_ACCOUNT_EMAIL_REGISTERED, $backUrl = '', $storeId = null, $sendemailStoreId = null)
    {
        $extensionAttributes = $customer->getExtensionAttributes();
        $customerExtension = $extensionAttributes ? $extensionAttributes : $this->getCustomerExtensionFactory()->create();
        $couponCode = $customer->getCustomAttribute('marketing_registration_coupon_code');
        $customerExtension->setMarketingRegistrationCouponCode($couponCode ? $couponCode->getValue() : null);

        $customer->setExtensionAttributes($customerExtension);
    }
}
