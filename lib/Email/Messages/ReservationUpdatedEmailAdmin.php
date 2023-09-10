<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/Email/Messages/ReservationCreatedEmailAdmin.php');

class ReservationUpdatedEmailAdmin extends ReservationCreatedEmailAdmin
{
	public function Subject()
	{
        return $this->Translate('ReservationUpdatedAdminSubjectWithResource', [$this->resource->GetName()]);
	}
}