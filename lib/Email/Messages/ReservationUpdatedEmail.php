<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Email/Messages/ReservationEmailMessage.php');

class ReservationUpdatedEmail extends ReservationEmailMessage
{
	public function Subject()
    {
        return $this->Translate('ReservationUpdatedSubjectWithResource', [$this->primaryResource->GetName()]);
    }

    protected function PopulateTemplate()
    {
        parent::PopulateTemplate();
        $this->Set('CreatedBy', null);
        $this->Set('UpdatedBy', $this->GetBookedBy());
    }

    protected function GetAction()
    {
        return ReservationAction::Update;
    }

    protected function GetTemplateName()
    {
        return 'ReservationCreated.tpl';
    }

    protected function IncludePrivateAttributes()
    {
        return true;
    }
}