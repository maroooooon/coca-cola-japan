<?php

namespace Coke\Logicbroker\Preference;

class Helper extends \Logicbroker\RetailerAPI\Helper\Data
{
    public function setApiKey($key) {
        $this->apiKey = $key;
    }

	/**
	 * This function is used to check if the Send Transaction Details is enabled or not.
	 *
	 * @param int $storeId The store ID of the order.
	 */
	public function getSendTransactionDetailsIsEnabled(int $storeId = null): bool
	{
		return $this->scopeConfig->isSetFlag('logicbroker_retailerapi_options/options/send_transaction_info', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
	}
}
