<?php

namespace Coke\User\Model;

use Magento\Framework\Validator\NotEmpty;
use Magento\Framework\Validator\Regex;
use Magento\Framework\Validator\StringLength;

class UserValidationRules extends \Magento\User\Model\UserValidationRules
{
    const MIN_PASSWORD_LENGTH = 8;

    /**
     * Adds validation rule for user password
     *
     * @param \Magento\User\Model\UserValidationRules $subject
     * @param \Magento\Framework\Validator\DataObject $validator
     * @param \Closure $proceed
     * @return \Magento\Framework\Validator\DataObject
     * @throws \Zend_Validate_Exception
     */
    public function addPasswordRules(\Magento\Framework\Validator\DataObject $validator)
    {
        $passwordNotEmpty = new NotEmpty();
        $passwordNotEmpty->setMessage(__('Password is required field.'), NotEmpty::IS_EMPTY);
        $minPassLength = self::MIN_PASSWORD_LENGTH;
        $passwordLength = new StringLength(['min' => $minPassLength, 'encoding' => 'UTF-8']);
        $passwordLength->setMessage(
            __('Your password must be at least %1 characters.', $minPassLength),
            \Zend_Validate_StringLength::TOO_SHORT
        );
        $passwordChars = new Regex(
            '/[a-z].*\d.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]|[a-z].*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?].*\d|[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?].*\d.*[a-z]|[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?].*[a-z].*\d|\d.*[a-z].*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]|\d.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?].*[a-z]/iu'
        );
        $passwordChars->setMessage(
            __('Your password must include one alpha, numeric, and special character.'),
            \Zend_Validate_Regex::NOT_MATCH
        );
        $validator->addRule(
            $passwordNotEmpty,
            'password'
        )->addRule(
            $passwordLength,
            'password'
        )->addRule(
            $passwordChars,
            'password'
        );

        return $validator;
    }
}
