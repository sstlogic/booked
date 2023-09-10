<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Email/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationEmailMessage.php');

class ReservationApprovedEmail extends ReservationEmailMessage
{
	public function Subject()
	{
		return $this->Translate('ReservationApprovedSubjectWithResource', [$this->primaryResource->GetName()]);
	}

	protected function PopulateTemplate()
	{
		parent::PopulateTemplate();
        $this->Set('CreatedBy', null);
		$this->Set('ApprovedBy', $this->GetBookedBy());
	}

    protected function GetAction()
    {
        return ReservationAction::Approve;
    }

    /**
     * @return string
     */
    protected function GetTemplateName()
    {
        return 'ReservationCreated.tpl';
    }

    protected function IncludePrivateAttributes()
    {
        return true;
    }
}