<?php

namespace Coke\ContactAgeRestrict\Plugin;

use Coke\ContactAgeRestrict\Helper\Config;
use Magento\Framework\App\RequestInterface;

class AccountManagementInterfacePlugin
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Config
     */
    private $config;

    /**
     * @param RequestInterface $request
     * @param Config $config
     */
    public function __construct(
        RequestInterface $request,
        Config $config
    ) {
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @param \Magento\Customer\Api\AccountManagementInterface $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param null $password
     * @param string $redirectUrl
     * @return array
     */
    public function beforeCreateAccount(
        \Magento\Customer\Api\AccountManagementInterface $subject,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
         $password = null,
         $redirectUrl = ''
    ) {
        if (!$this->config->canSaveDob()) {
            return [$customer, $password, $redirectUrl];
        }

        if ($dob = $this->getDob($this->request)) {
            $customer->setDob($dob);
        }

        return [$customer, $password, $redirectUrl];
    }

    /**
     * @param RequestInterface $request
     * @return string|null
     */
    private function getDob(RequestInterface $request): ?string
    {
        $params = $request->getParams();
        if (isset($params['year'], $params['month'], $params['day'])) {
            return sprintf('%s-%s-%s', $params['year'], $params['month'], $params['day']);
        }

        return null;
    }
}
