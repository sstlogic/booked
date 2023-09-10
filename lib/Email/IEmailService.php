<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IEmailService
{
	/**
	 * @param IEmailMessage $emailMessage
	 */
	function Send(IEmailMessage $emailMessage);
}
