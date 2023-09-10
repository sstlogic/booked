<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Presenters/NotificationPreferencesPresenter.php');

interface INotificationPreferencesPage extends IActionPage
{
    /**
     * @param bool $enabled
     */
    public function SetEmailEnabled($enabled);

    /**
     * @param bool $enabled
     */
    public function SetApprovedEmail($enabled);

    /**
     * @param bool $enabled
     */
    public function SetCreatedEmail($enabled);

    /**
     * @param bool $enabled
     */
    public function SetUpdatedEmail($enabled);

    /**
     * @param bool $enabled
     */
    public function SetDeletedEmail($enabled);

    /**
     * @param bool $enabled
     */
    public function SetSeriesEndingEmail($enabled);

    /**
     * @param bool $enabled
     */
    public function SetParticipantChangedEmail($enabled);

    /**
     * @param bool $enabled
     */
    public function SetMissedCheckinEmail($enabled);

    /**
     * @param bool $enabled
     */
    public function SetMissedCheckoutEmail($enabled);

    /**
     * @param bool $enabled
     */
    public function SetApprovedSms($enabled);

    /**
     * @param bool $enabled
     */
    public function SetCreatedSms($enabled);

    /**
     * @param bool $enabled
     */
    public function SetUpdatedSms($enabled);

    /**
     * @param bool $enabled
     */
    public function SetDeletedSms($enabled);

    /**
     * @param bool $enabled
     */
    public function SetSeriesEndingSms($enabled);

    /**
     * @param bool $enabled
     */
    public function SetParticipantChangedSms($enabled);

    /**
     * @param bool $enabled
     */
    public function SetMissedCheckinSms($enabled);

    /**
     * @param bool $enabled
     */
    public function SetMissedCheckoutSms($enabled);

    /**
     * @param bool $enabled
     */
    public function SetRemindersSms($enabled);

    /**
     * @return bool
     */
    public function GetApprovedEmail();

    /**
     * @return bool
     */
    public function GetCreatedEmail();

    /**
     * @return bool
     */
    public function GetUpdatedEmail();

    /**
     * @return bool
     */
    public function GetDeletedEmail();

    /**
     * @return bool
     */
    public function GetSeriesEndingEmail();

    /**
     * @return bool
     */
    public function GetParticipantChangedEmail();

    /**
     * @return bool
     */
    public function GetMissedCheckinEmail();

    /**
     * @return bool
     */
    public function GetMissedCheckoutEmail();

    /**
     * @return bool
     */
    public function GetApprovedSms();

    /**
     * @return bool
     */
    public function GetCreatedSms();

    /**
     * @return bool
     */
    public function GetUpdatedSms();

    /**
     * @return bool
     */
    public function GetDeletedSms();

    /**
     * @return bool
     */
    public function GetSeriesEndingSms();

    /**
     * @return bool
     */
    public function GetParticipantChangedSms();

    /**
     * @return bool
     */
    public function GetMissedCheckinSms();

    /**
     * @return bool
     */
    public function GetMissedCheckoutSms();

    /**
     * @return bool
     */
    public function GetRemindersSms();

    /**
     * @param bool $werePreferencesUpdated
     */
    public function SetPreferencesSaved($werePreferencesUpdated);

    /**
     * @param bool $enabled
     */
    public function SetParticipationEnabled($enabled);

    public function SetSmsEnabled(bool $isSmsEnabled);

    public function SetPhoneNumber(?string $phone);

    public function SetIsSmsOptedIn(bool $isOptedIn);

    public function SetIsSmsPhoneNumberNeeded(bool $isNeeded);

    public function SetIsSupportedSmsRegion(bool $isSupported);

    public function SetIsAwaitingSmsConfirmation(bool $isWaiting);

    /**
     * @return string
     */
    public function GetConfirmedSmsCode();
}

class NotificationPreferencesPage extends ActionPage implements INotificationPreferencesPage
{
    /**
     * @var NotificationPreferencesPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('NotificationPreferences');
        $this->presenter = new NotificationPreferencesPresenter($this, new UserRepository(), new SmsService());
    }

    public function SetApprovedEmail($enabled)
    {
        $this->Set('ApprovedEmail', $enabled);
    }

    public function SetCreatedEmail($enabled)
    {
        $this->Set('CreatedEmail', $enabled);
    }

    public function SetUpdatedEmail($enabled)
    {
        $this->Set('UpdatedEmail', $enabled);
    }

    public function SetDeletedEmail($enabled)
    {
        $this->Set('DeletedEmail', $enabled);
    }

    public function SetSeriesEndingEmail($enabled)
    {
        $this->Set('SeriesEndingEmail', $enabled);
    }

    public function SetParticipantChangedEmail($enabled)
    {
        $this->Set('ParticipantChangedEmail', $enabled);
    }

    public function SetMissedCheckinEmail($enabled)
    {
        $this->Set('MissedCheckinEmail', $enabled);
    }

    public function SetMissedCheckoutEmail($enabled)
    {
        $this->Set('MissedCheckoutEmail', $enabled);
    }

    public function SetApprovedSms($enabled)
    {
        $this->Set('ApprovedSms', $enabled);
    }

    public function SetRemindersSms($enabled)
    {
        $this->Set('RemindersSms', $enabled);
    }

    public function SetCreatedSms($enabled)
    {
        $this->Set('CreatedSms', $enabled);
    }

    public function SetUpdatedSms($enabled)
    {
        $this->Set('UpdatedSms', $enabled);
    }

    public function SetDeletedSms($enabled)
    {
        $this->Set('DeletedSms', $enabled);
    }

    public function SetSeriesEndingSms($enabled)
    {
        $this->Set('SeriesEndingSms', $enabled);
    }

    public function SetParticipantChangedSms($enabled)
    {
        $this->Set('ParticipantChangedSms', $enabled);
    }

    public function SetMissedCheckinSms($enabled)
    {
        $this->Set('MissedCheckinSms', $enabled);
    }

    public function SetMissedCheckoutSms($enabled)
    {
        $this->Set('MissedCheckoutSms', $enabled);
    }

    public function GetApprovedEmail()
    {
        return $this->GetCheckbox(FormKeys::N_EMAIL_APPROVED);
    }

    public function GetCreatedEmail()
    {
        return $this->GetCheckbox(FormKeys::N_EMAIL_CREATED);
    }

    public function GetUpdatedEmail()
    {
        return $this->GetCheckbox(FormKeys::N_EMAIL_UPDATED);
    }

    public function GetDeletedEmail()
    {
        return $this->GetCheckbox(FormKeys::N_EMAIL_DELETED);
    }

    public function GetSeriesEndingEmail()
    {
        return $this->GetCheckbox(FormKeys::N_EMAIL_SERIES_ENDING);
    }

    public function GetParticipantChangedEmail()
    {
        return $this->GetCheckbox(FormKeys::N_EMAIL_PARTICIPANT);
    }

    public function GetMissedCheckinEmail()
    {
        return $this->GetCheckbox(FormKeys::N_EMAIL_MISSED_CHECKIN);
    }

    public function GetMissedCheckOutEmail()
    {
        return $this->GetCheckbox(FormKeys::N_EMAIL_MISSED_CHECKOUT);
    }

    public function GetRemindersSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_REMINDER);
    }

    public function GetApprovedSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_APPROVED);
    }

    public function GetCreatedSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_CREATED);
    }

    public function GetUpdatedSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_UPDATED);
    }

    public function GetDeletedSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_DELETED);
    }

    public function GetSeriesEndingSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_SERIES_ENDING);
    }

    public function GetParticipantChangedSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_PARTICIPANT);
    }

    public function GetMissedCheckinSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_MISSED_CHECKIN);
    }

    public function GetMissedCheckoutSms()
    {
        return $this->GetCheckbox(FormKeys::N_SMS_MISSED_CHECKOUT);
    }

    public function SetPreferencesSaved($werePreferencesUpdated)
    {
        $this->Set('PreferencesUpdated', $werePreferencesUpdated);
    }

    public function SetEmailEnabled($enabled)
    {
        $this->Set('EmailEnabled', $enabled);
    }

    public function SetParticipationEnabled($enabled)
    {
        $this->Set('ParticipationEnabled', $enabled);
    }

    public function SetSmsEnabled(bool $isSmsEnabled)
    {
        $this->Set('IsSmsEnabled', $isSmsEnabled);
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
    }

    public function ProcessPageLoad()
    {
        $this->Set('EmailEnabled', false);
        $this->Set('IsSmsEnabled', false);

        $this->SetIsSmsOptedIn(false);
        $this->SetIsSmsPhoneNumberNeeded(false);
        $this->SetIsSupportedSmsRegion(true);
        $this->SetIsAwaitingSmsConfirmation(false);

        $this->presenter->PageLoad();
        $this->Display('MyAccount/notification-preferences.tpl');
    }

    public function SetPhoneNumber(?string $phone)
    {
        $this->Set('PhoneNumber', $phone);
    }

    public function SetIsSmsOptedIn(bool $isOptedIn)
    {
        $this->Set('IsSmsOptedIn', $isOptedIn);
    }

    public function SetIsSmsPhoneNumberNeeded(bool $isNeeded)
    {
        $this->Set('IsSmsPhoneNumberNeeded', $isNeeded);
    }

    public function SetIsSupportedSmsRegion(bool $isSupported)
    {
        $this->Set('IsSmsSupportedRegion', $isSupported);
    }

    public function SetIsAwaitingSmsConfirmation(bool $isWaiting)
    {
        $this->Set('IsSmsAwaitingConfirmation', $isWaiting);
    }

    public function GetConfirmedSmsCode()
    {
        return $this->GetForm(FormKeys::SMS_CONFIRMATION_CODE);
    }
}