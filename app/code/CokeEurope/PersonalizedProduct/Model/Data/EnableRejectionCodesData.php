<?php

namespace CokeEurope\PersonalizedProduct\Model\Data;

use CokeEurope\PersonalizedProduct\Api\Data\EnableRejectionCodesInterface;
use Magento\Framework\DataObject;

class EnableRejectionCodesData extends DataObject implements EnableRejectionCodesInterface
{
	/**
	 * Getter for Id.
	 *
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->getData(self::ID) === null ? null
			: (int)$this->getData(self::ID);
	}
	
	/**
	 * Setter for Id.
	 *
	 * @param int|null $id
	 *
	 * @return void
	 */
	public function setId(?int $id): void
	{
		$this->setData(self::ID, $id);
	}
	
	/**
	 * Getter for Code.
	 *
	 * @return int|null
	 */
	public function getCode(): ?int
	{
		return $this->getData(self::CODE) === null ? null
			: (int)$this->getData(self::CODE);
	}
	
	/**
	 * Setter for Code.
	 *
	 * @param int|null $code
	 *
	 * @return void
	 */
	public function setCode(?int $code): void
	{
		$this->setData(self::CODE, $code);
	}
	
	/**
	 * Getter for ShortDescription.
	 *
	 * @return string|null
	 */
	public function getShortDescription(): ?string
	{
		return $this->getData(self::SHORT_DESCRIPTION);
	}
	
	/**
	 * Setter for ShortDescription.
	 *
	 * @param string|null $shortDescription
	 *
	 * @return void
	 */
	public function setShortDescription(?string $shortDescription): void
	{
		$this->setData(self::SHORT_DESCRIPTION, $shortDescription);
	}
	
	/**
	 * Getter for LongDescription.
	 *
	 * @return string|null
	 */
	public function getLongDescription(): ?string
	{
		return $this->getData(self::LONG_DESCRIPTION);
	}
	
	/**
	 * Setter for LongDescription.
	 *
	 * @param string|null $longDescription
	 *
	 * @return void
	 */
	public function setLongDescription(?string $longDescription): void
	{
		$this->setData(self::LONG_DESCRIPTION, $longDescription);
	}
}
