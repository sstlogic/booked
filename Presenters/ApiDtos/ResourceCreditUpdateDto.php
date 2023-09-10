<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ResourceCreditUpdateDto
{
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var float
	 */
	public $creditsOffPeak;
	/**
	 * @var float
	 */
	public $creditsPeak;
	/**
	 * @var CreditApplicability
	 */
	public $creditsCalculated;
	/**
	 * @var bool
	 */
	public $creditsChargedForBlockedSlots;
}
