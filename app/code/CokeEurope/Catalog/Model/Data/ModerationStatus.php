<?php

namespace CokeEurope\Catalog\Model\Data;

use CokeEurope\Catalog\Api\Data\ModerationStatusInterface;
use Magento\Framework\DataObject;

class ModerationStatus extends DataObject implements ModerationStatusInterface
{
	/**
	 * @inheritDoc
	 */
	public function getModerationStatus(): ?int
	{
		return $this->getData(self::MODERATION_STATUS_CODE);
	}
	
	/**
	 * @inheritDoc
	 */
	public function setModerationStatus(?int $moderationStatus): void
	{
		$this->setData(self::MODERATION_STATUS_CODE, $moderationStatus);
	}
	
	public function getModerationText(int $moderationStatus): string
	{
		return self::MODERATION_STATUS_TEXTS[$moderationStatus];
	}
}
