<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IValidator
{
	/**
	 * @return bool
	 */
	public function IsValid();

	/**
	 * @return void
	 */
	public function Validate();

	/**
	 * @return string[]|null
	 */
	public function Messages();

	/**
	 * @return bool
	 */
	public function ReturnsErrorResponse();
}