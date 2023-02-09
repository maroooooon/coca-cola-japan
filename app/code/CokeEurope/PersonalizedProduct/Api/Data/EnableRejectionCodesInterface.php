<?php

namespace CokeEurope\PersonalizedProduct\Api\Data;

interface EnableRejectionCodesInterface
{
	/**
	 * String constants for property names
	 */
	const ID = "id";
	const CODE = "code";
	const SHORT_DESCRIPTION = "short_description";
	const LONG_DESCRIPTION = "long_description";
	const STORE_ID = "store_id";
	
	/**
	 * Getter for Id.
	 *
	 * @return int|null
	 */
	public function getId(): ?int;
	
	/**
	 * Setter for Id.
	 *
	 * @param int|null $id
	 *
	 * @return void
	 */
	public function setId(?int $id): void;
	
	/**
	 * Getter for Code.
	 *
	 * @return int|null
	 */
	public function getCode(): ?int;
	
	/**
	 * Setter for Code.
	 *
	 * @param int|null $code
	 *
	 * @return void
	 */
	public function setCode(?int $code): void;
	
	/**
	 * Getter for ShortDescription.
	 *
	 * @return string|null
	 */
	public function getShortDescription(): ?string;
	
	/**
	 * Setter for ShortDescription.
	 *
	 * @param string|null $shortDescription
	 *
	 * @return void
	 */
	public function setShortDescription(?string $shortDescription): void;
	
	/**
	 * Getter for LongDescription.
	 *
	 * @return string|null
	 */
	public function getLongDescription(): ?string;
	
	/**
	 * Setter for LongDescription.
	 *
	 * @param string|null $longDescription
	 *
	 * @return void
	 */
	public function setLongDescription(?string $longDescription): void;
}
