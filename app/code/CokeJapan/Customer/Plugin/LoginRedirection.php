<?php

namespace CokeJapan\Customer\Plugin;

use CokeJapan\Customer\Helper\Config;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;

class LoginRedirection
{


	private Config $configHelper;
	private StoreManagerInterface $storeManager;
	private Session $customerSession;

	public function __construct(
		Config $configHelper,
        StoreManagerInterface $storeManager,
        Session $customerSession
    )
    {
		$this->configHelper = $configHelper;
		$this->storeManager = $storeManager;
		$this->customerSession = $customerSession;
	}
    public function afterLoadCustomerQuote(
        \Magento\Checkout\Model\Session $subject,
        $result
    )
    {

		/* Skip this plugin if not enabled in system config */
		if(!$this->configHelper->redirectEnabled()){
			return;
		}

        if(parse_url($_SERVER['HTTP_REFERER'])["path"] === '/customer/account/create/' && isset($_POST['URL_Divide'])) {
			if ($_POST['URL_Divide'] !== 'URL_Divide_Value') {
				return;
			}

            $quote = $subject->getQuote();
            if (count($quote->getAllItems()) > 0) {
                $this->customerSession
                    ->setBeforeAuthUrl($this->storeManager->getStore()->getUrl('checkout'));
            }
        }
    }
}
