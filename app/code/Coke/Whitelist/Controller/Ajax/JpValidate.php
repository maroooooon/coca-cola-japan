<?php

namespace Coke\Whitelist\Controller\Ajax;

use Coke\Whitelist\Exception\WhitelistEntityContainsIllegalCharacterException;
use Coke\Whitelist\Exception\WhitelistEntityDeniedException;
use Coke\Whitelist\Exception\WhitelistEntityMaxLengthException;
use Coke\Whitelist\Exception\WhitelistEntityNotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;

class JpValidate extends Validate
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $value = $this->getRequest()->getParam('value');
            $typeId = $this->getRequest()->getParam('typeId');
            $validatedValue = $this->validatePhrase($value, $typeId);

            return $resultJson->setData([
                'status' => 'success',
                'validatedValue' => $validatedValue,
                'whitelist_value_status' => $this->getStatus($typeId, $validatedValue)
            ]);
        } catch (WhitelistEntityNotFoundException $e) {
            $resultJson->setHttpResponseCode(400);
            return $resultJson->setData(['status' => 'not_found', 'error' => $e->getMessage()]);
        } catch (WhitelistEntityDeniedException $e) {
            $resultJson->setHttpResponseCode(400);
            return $resultJson->setData(['status' => 'denied', 'error' => $e->getMessage()]);
        } catch (WhitelistEntityContainsIllegalCharacterException $e) {
            $resultJson->setHttpResponseCode(400);
            return $resultJson->setData(['status' => 'illegal_character', 'error' => $e->getMessage()]);
        } catch (WhitelistEntityMaxLengthException $e) {
            $resultJson->setHttpResponseCode(400);
            return $resultJson->setData(['status' => 'error', 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            $resultJson->setHttpResponseCode(400);
            $this->logger->error($e->getMessage());
            return $resultJson->setData(['status' => 'error', 'error' => 'Unknown error. Please contact customer service.']);
        }
    }

    /**
     * @param $value
     * @param $typeId
     * @return string
     * @throws \Coke\Whitelist\Exception\WhitelistEntityContainsIllegalCharacterException
     * @throws \Coke\Whitelist\Exception\WhitelistEntityDeniedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws WhitelistEntityMaxLengthException
     */
    protected function validatePhrase($value, $typeId): string
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->whitelistRepository->isDeniedValue($value, $storeId, $typeId);
        $this->whitelistRepository->containsIllegalCharacter($value, $typeId);
        $this->whiteListHelper->validateMaxLength($value);

        return $value;
    }

    /**
     * @param int $typeId
     * @param string $value
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStatus(int $typeId, string $value): ?string
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->whiteListHelper->getWhitelistValueStatus($typeId, $value, $storeId);
    }
}
