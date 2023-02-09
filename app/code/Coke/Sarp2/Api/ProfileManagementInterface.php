<?php

namespace Coke\Sarp2\Api;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;

interface ProfileManagementInterface
{
    /**
     * @param int $profileId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updatePayments(int $profileId): bool;
}
