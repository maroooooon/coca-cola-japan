<?php

namespace Coke\Customer\Controller\Account;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;

class EditPost extends \Magento\Customer\Controller\Account\EditPost
{
    /**
     * Change customer password
     *
     * @param string $email
     * @return boolean
     * @throws InvalidEmailOrPasswordException|InputException
     */
    protected function changeCustomerPassword($email)
    {
        $isPasswordChanged = false;
        if ($this->getRequest()->getParam('change_password')) {
            $currPass = $this->getRequest()->getPost('current_password');
            $newPass = $this->getRequest()->getPost('password');
            $confPass = $this->getRequest()->getPost('password_confirmation');
            if ($currPass == $newPass) {
                throw new InputException(__('The password you have entered is the same as your previous password'));
            }
            if ($newPass != $confPass) {
                throw new InputException(__('Password confirmation doesn\'t match entered password.'));
            }

            $isPasswordChanged = $this->accountManagement->changePassword($email, $currPass, $newPass);
        }

        return $isPasswordChanged;
    }
}
