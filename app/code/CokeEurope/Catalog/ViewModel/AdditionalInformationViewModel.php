<?php

namespace CokeEurope\Catalog\ViewModel;

use CokeEurope\Catalog\Api\Data\ModerationStatusInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn;
use Magento\Sales\Model\Order\Item;

class AdditionalInformationViewModel implements ArgumentInterface
{
	private DefaultColumn $data;
	private ModerationStatusInterface $moderationStatusInterface;
	
	/**
	 * @param DefaultColumn $data
	 * @param ModerationStatusInterface $moderationStatusInterface
	 */
	public function __construct(
		DefaultColumn $data,
		ModerationStatusInterface $moderationStatusInterface
	)
	{
		$this->data = $data;
		$this->moderationStatusInterface = $moderationStatusInterface;
	}
	
	/**
	 * It returns the moderation status text for an order item
	 *
	 * @return string The moderation status text.
	 */
	public function getModerationStatusText(): string
	{
		if ($this->data->getItem()->getModerationStatus()){
			return $this->moderationStatusInterface->getModerationText($this->data->getItem()->getModerationStatus());
		}
		return 'N/A';
	}
}
