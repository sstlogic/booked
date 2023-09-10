<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/SMS/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');

class NotificationPreferencesPresenter extends ActionPresenter
{
    /**
     * @var INotificationPreferencesPage
     */
    private $page;

    /**
     * @var IUserRepository
     */
    private $userRepository;

    /**
     * @var ISmsService
     */
    private $smsService;

    public function __construct(
        INotificationPreferencesPage $page,
        IUserRepository              $userRepository,
        ISmsService                  $smsService)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->userRepository = $userRepository;
        $this->smsService = $smsService;

        $this->AddAction("updateEmail", 'UpdateProfile');
        $this->AddAction("sendConfirmationCode", 'SendConfirmationCode');
        $this->AddAction("confirmSmsCode", 'ConfirmSmsCode');
    }

    public function PageLoad()
    {
        $this->page->SetEmailEnabled(Configuration::Instance()->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter()));
        $this->page->SetSmsEnabled($this->smsService->IsEnabled());
        $this->page->SetParticipationEnabled(!Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_PREVENT_PARTICIPATION, new BooleanConverter()));

        $userSession = ServiceLocator::GetServer()->GetUserSession();
        $user = $this->userRepository->LoadById($userSession->UserId);

        $this->page->SetApprovedEmail($user->WantsEventEmail(new ReservationApprovedEvent()));
        $this->page->SetCreatedEmail($user->WantsEventEmail(new ReservationCreatedEvent()));
        $this->page->SetUpdatedEmail($user->WantsEventEmail(new ReservationUpdatedEvent()));
        $this->page->SetDeletedEmail($user->WantsEventEmail(new ReservationDeletedEvent()));
        $this->page->SetSeriesEndingEmail($user->WantsEventEmail(new ReservationSeriesEndingEvent()));
        $this->page->SetParticipantChangedEmail($user->WantsEventEmail(new ParticipationChangedEvent()));
        $this->page->SetMissedCheckinEmail($user->WantsEventEmail(new ReservationMissedCheckinEvent()));
        $this->page->SetMissedCheckoutEmail($user->WantsEventEmail(new ReservationMissedCheckoutEvent()));

        $this->page->SetRemindersSms($user->WantsEventSms(new ReservationReminderEvent()));
        $this->page->SetApprovedSms($user->WantsEventSms(new ReservationApprovedEvent()));
        $this->page->SetCreatedSms($user->WantsEventSms(new ReservationCreatedEvent()));
        $this->page->SetUpdatedSms($user->WantsEventSms(new ReservationUpdatedEvent()));
        $this->page->SetDeletedSms($user->WantsEventSms(new ReservationDeletedEvent()));
        $this->page->SetSeriesEndingSms($user->WantsEventSms(new ReservationSeriesEndingEvent()));
        $this->page->SetParticipantChangedSms($user->WantsEventSms(new ParticipationChangedEvent()));
        $this->page->SetMissedCheckinSms($user->WantsEventSms(new ReservationMissedCheckinEvent()));
        $this->page->SetMissedCheckoutSms($user->WantsEventSms(new ReservationMissedCheckoutEvent()));

        $this->SetSmsConfiguration($user);
    }

    public function UpdateProfile()
    {
        $userSession = ServiceLocator::GetServer()->GetUserSession();
        $user = $this->userRepository->LoadById($userSession->UserId);

        $user->ChangeEmailPreference(new ReservationApprovedEvent(), $this->page->GetApprovedEmail());
        $user->ChangeEmailPreference(new ReservationCreatedEvent(), $this->page->GetCreatedEmail());
        $user->ChangeEmailPreference(new ReservationUpdatedEvent(), $this->page->GetUpdatedEmail());
        $user->ChangeEmailPreference(new ReservationDeletedEvent(), $this->page->GetDeletedEmail());
        $user->ChangeEmailPreference(new ReservationSeriesEndingEvent(), $this->page->GetSeriesEndingEmail());
        $user->ChangeEmailPreference(new ParticipationChangedEvent(), $this->page->GetParticipantChangedEmail());
        $user->ChangeEmailPreference(new ReservationMissedCheckinEvent(), $this->page->GetMissedCheckinEmail());
        $user->ChangeEmailPreference(new ReservationMissedCheckoutEvent(), $this->page->GetMissedCheckoutEmail());

        $user->ChangeSmsPreference(new ReservationReminderEvent(), $this->page->GetRemindersSms());
        $user->ChangeSmsPreference(new ReservationApprovedEvent(), $this->page->GetApprovedSms());
        $user->ChangeSmsPreference(new ReservationCreatedEvent(), $this->page->GetCreatedSms());
        $user->ChangeSmsPreference(new ReservationUpdatedEvent(), $this->page->GetUpdatedSms());
        $user->ChangeSmsPreference(new ReservationDeletedEvent(), $this->page->GetDeletedSms());
        $user->ChangeSmsPreference(new ReservationSeriesEndingEvent(), $this->page->GetSeriesEndingSms());
        $user->ChangeSmsPreference(new ParticipationChangedEvent(), $this->page->GetParticipantChangedSms());
        $user->ChangeSmsPreference(new ReservationMissedCheckinEvent(), $this->page->GetMissedCheckinSms());
        $user->ChangeSmsPreference(new ReservationMissedCheckoutEvent(), $this->page->GetMissedCheckoutSms());

        $this->userRepository->Update($user);

        $this->page->SetPreferencesSaved(true);
    }

    private function SetSmsConfiguration(User $user)
    {
        $this->page->SetPhoneNumber($user->Phone());
        if ($user->IsSmsOptedIn()) {
            $this->page->SetIsSmsOptedIn(true);
        } else {
            $this->page->SetIsSmsPhoneNumberNeeded(empty($user->Phone()));
            $this->page->SetIsSupportedSmsRegion($user->IsSmsRegionSupported());
            $this->page->SetIsAwaitingSmsConfirmation($user->HasOutstandingSmsConfirmation());
        }
    }

    public function SendConfirmationCode()
    {
        $otp = $this->smsService->GetOneTimeCode();
        $userSession = ServiceLocator::GetServer()->GetUserSession();

        $user = $this->userRepository->LoadById($userSession->UserId);

        Log::Debug('Sending SMS confirmation code.', ['userId' => $userSession->UserId, 'otp' => $otp]);

        $body = Resources::GetInstance()->GetString('SMSMessageOTP', [$otp]);
        $result = $this->smsService->Send(new SmsMessage($user->PhoneWithCountryCode(), $body));

        if ($result->IsSuccess()) {
            $this->userRepository->AddSmsConfiguration($userSession->UserId, $otp);
        }
    }

    public function ConfirmSmsCode()
    {
        $userSession = ServiceLocator::GetServer()->GetUserSession();
        $otp = $this->page->GetConfirmedSmsCode();

        Log::Debug('Confirming SMS confirmation code.', ['userId' => $userSession->UserId, 'otp' => $otp]);

        $user = $this->userRepository->LoadById($userSession->UserId);

        if ($user->IsSmsConfirmationCodeValid($otp)) {
            $this->userRepository->UpdateSmsConfiguration($user->SmsConfigId(), Date::Now(), null);
            $body = Resources::GetInstance()->GetString('SMSMessageOTPConfirm');
            $this->smsService->Send(new SmsMessage($user->PhoneWithCountryCode(), $body));
        }
    }
}

