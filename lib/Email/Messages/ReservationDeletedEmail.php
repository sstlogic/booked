<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/Messages/ReservationEmailMessage.php');

class ReservationDeletedEmail extends ReservationEmailMessage
{
	/**
	 * @return string
	 */
	public function Subject()
	{
		return $this->Translate('ReservationDeletedSubjectWithResource', [$this->primaryResource->GetName()]);
	}

	public function PopulateTemplate()
	{
        $this->showQrCode = false;
		parent::PopulateTemplate();
		if (method_exists($this->reservationSeries, 'GetDeleteReason'))
		{
			$this->Set('DeleteReason', $this->reservationSeries->GetDeleteReason());
		}
		$this->Set("Deleted", true);
	}

    protected function GetAction()
    {
        return ReservationAction::Delete;
    }

	protected function GetTemplateName()
	{
		return 'ReservationDeleted.tpl';
	}

    protected function IncludePrivateAttributes()
    {
        return true;
    }
}