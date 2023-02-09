<?php

namespace CokeEurope\Catalog\Api\Data;

interface ModerationStatusInterface
{
	/**
	 * String constants for property names
	 */
	const MODERATION_STATUS_CODE = 'moderation_status';
	const MODERATION_STATUS_REJECTED = 0;
	const MODERATION_STATUS_PENDING = 1;
	const MODERATION_STATUS_APPROVED = 2;
	
	
	const MODERATION_STATUS_TEXTS = [
		0 => "Rejected",
		1 => "Pending Approval",
		2 => "Approved"
	];
	
	/**
	 * Getter for ModerationStatus.
	 *
	 * @return int|null
	 */
	public function getModerationStatus(): ?int;
	
	/**
	 * Setter for ModerationStatus.
	 *
	 * @param int|null $moderationStatus
	 *
	 * @return void
	 */
	public function setModerationStatus(int $moderationStatus): void;
	
	/**
	 * @param int $moderationStatus
	 * @return string
	 */
	public function getModerationText(int $moderationStatus): string;
}
