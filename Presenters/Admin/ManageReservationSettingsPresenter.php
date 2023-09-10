<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Pages/Admin/ManageReservationSettingsPage.php');

class ManageReservationSettingsPresenter extends ActionPresenter
{
    /**
     * @var IManageReservationSettingsPage
     */
    private $page;
    /**
     * @var IConfigurationSettings
     */
    private $configSettings;
    /**
     * @var IGroupViewRepository
     */
    private $groupViewRepository;

    public function __construct(IManageReservationSettingsPage $page, IConfigurationSettings $configSettings, IGroupViewRepository $groupViewRepository)
    {

        parent::__construct($page);
        $this->page = $page;
        $this->configSettings = $configSettings;
        $this->groupViewRepository = $groupViewRepository;

        $this->AddAction("Save", "SaveSettings");
    }

    public function PageLoad()
    {
        $config = Configuration::Instance();
        $this->page->SetRequireTitle($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_TITLE_REQUIRED, new BooleanConverter()));
        $this->page->SetRequireDescription($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_TITLE_REQUIRED, new BooleanConverter()));
        $this->page->SetRemindersEnabled($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_REMINDERS_ENABLED, new BooleanConverter()));
        $this->page->SetAllowGuests($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_GUESTS, new BooleanConverter()));
        $this->page->SetAllowParticipation(!$config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_PREVENT_PARTICIPATION, new BooleanConverter()));
        $this->page->SetAllowWaitlist($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_WAITLIST, new BooleanConverter()));
        $this->page->SetAllowRecurrence(!$config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_PREVENT_RECURRENCE, new BooleanConverter()));
        $this->page->SetRequireUpdateApproval($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_UPDATES_REQUIRE_APPROVAL, new BooleanConverter()));
        $this->page->SetShowDetailedSave($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_SHOW_DETAILED_SAVE_RESPONSE, new BooleanConverter()));
        $this->page->SetShowEmail(!$config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_HIDE_EMAIL, new BooleanConverter()));
        $start = $this->GetReminderPieces($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_REMINDER));
        $end = $this->GetReminderPieces($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_END_REMINDER));
        $this->page->SetStartReminder($start['value'], $start['interval']);
        $this->page->SetEndReminder($end['value'], $end['interval']);
        $this->page->SetCheckinMinutes($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_CHECKIN_MINUTES, new IntConverter()));
        $this->page->SetMaxCheckboxes($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_MAXIMUM_RESOURCE_CHECKLIST, new IntConverter()));
        $this->page->SetStartConstraint($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_TIME_CONSTRAINT));
        $this->page->SetLimitInvitees($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_LIMIT_INVITEES_TO_MAX_PARTICIPANTS, new BooleanConverter()));
        $this->page->SetAllowMeetingLinks($config->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_MEETING_LINKS, new BooleanConverter()));

        $groupIds = [];
        $groups = $this->groupViewRepository->GetList()->Results();
        /** @var GroupItemView $group */
        foreach ($groups as $group) {
            if ($group->LimitedOnReservation()) {
                $groupIds[] = $group->Id();
            }
        }
        $this->page->SetGroups($groups, $groupIds);
    }

    public function SaveSettings()
    {
        $file = ROOT_DIR . 'config/config.php';
        $settings = $this->configSettings->GetSettings($file);

        $reservationSection = $settings[ConfigSection::RESERVATION];

        $reservationSection[ConfigKeys::RESERVATION_TITLE_REQUIRED] = $this->AsBoolString($this->page->GetTitleRequired());
        $reservationSection[ConfigKeys::RESERVATION_DESCRIPTION_REQUIRED] = $this->AsBoolString($this->page->GetDescriptionRequired());
        $reservationSection[ConfigKeys::RESERVATION_REMINDERS_ENABLED] = $this->AsBoolString($this->page->GetRemindersEnabled());
        $reservationSection[ConfigKeys::RESERVATION_ALLOW_GUESTS] = $this->AsBoolString($this->page->GetAllowGuests());
        $reservationSection[ConfigKeys::RESERVATION_ALLOW_WAITLIST] = $this->AsBoolString($this->page->GetAllowWaitlist());
        $reservationSection[ConfigKeys::RESERVATION_PREVENT_PARTICIPATION] = $this->AsBoolString($this->page->GetAllowParticipation(), true);
        $reservationSection[ConfigKeys::RESERVATION_PREVENT_RECURRENCE] = $this->AsBoolString($this->page->GetAllowRecurrence(), true);
        $reservationSection[ConfigKeys::RESERVATION_SHOW_DETAILED_SAVE_RESPONSE] = $this->AsBoolString($this->page->GetShowDetailedSave());
        $reservationSection[ConfigKeys::RESERVATION_UPDATES_REQUIRE_APPROVAL] = $this->AsBoolString($this->page->GetUpdatesRequireApproval());
        $reservationSection[ConfigKeys::RESERVATION_HIDE_EMAIL] = $this->AsBoolString($this->page->GetShowEmail(), true);
        $reservationSection[ConfigKeys::RESERVATION_CHECKIN_MINUTES] = $this->AsIntString($this->page->GetCheckinMinutes());
        $reservationSection[ConfigKeys::RESERVATION_MAXIMUM_RESOURCE_CHECKLIST] = $this->AsIntString($this->page->GetCheckboxLimit());
        $reservationSection[ConfigKeys::RESERVATION_LIMIT_INVITEES_TO_MAX_PARTICIPANTS] = $this->AsBoolString($this->page->GetLimitInvites());
        $reservationSection[ConfigKeys::RESERVATION_ALLOW_MEETING_LINKS] = $this->AsBoolString($this->page->GetAllowMeetingLinks());

        $constraint = ReservationStartTimeConstraint::_DEFAULT;
        $submittedConstraint = $this->page->GetStartTimeConstraint();
        if (ReservationStartTimeConstraint::IsCurrent($submittedConstraint)) {
            $constraint = ReservationStartTimeConstraint::CURRENT;
        }
        if (ReservationStartTimeConstraint::IsNone($submittedConstraint)) {
            $constraint = ReservationStartTimeConstraint::NONE;
        }
        $reservationSection[ConfigKeys::RESERVATION_START_TIME_CONSTRAINT] = $constraint;

        $startReminderEnabled = $this->page->IsStartReminderEnabled();
        $startValue = $this->AsIntString($this->page->GetStartReminderValue());
        if ($startReminderEnabled && $startValue != "") {
            $interval = $this->page->GetStartReminderInterval();
            $reservationSection[ConfigKeys::RESERVATION_START_REMINDER] = "$startValue $interval";
        } else {
            $reservationSection[ConfigKeys::RESERVATION_START_REMINDER] = '';
        }

        $endReminderEnabled = $this->page->IsEndReminderEnabled();
        $endValue = $this->AsIntString($this->page->GetEndReminderValue());
        if ($endReminderEnabled && $endValue != "") {
            $interval = $this->page->GetEndReminderInterval();
            $reservationSection[ConfigKeys::RESERVATION_END_REMINDER] = "$endValue $interval";
        } else {
            $reservationSection[ConfigKeys::RESERVATION_END_REMINDER] = '';
        }

        $settings[ConfigSection::RESERVATION] = $reservationSection;

        Log::Debug('Updating reservation settings');

        $this->configSettings->WriteSettings($file, $settings);

        ServiceLocator::GetDatabase()->Execute(new ClearGroupReservationLimitsCommand());
        $groupIds = array_map('intval', $this->page->GetLimitedGroupIds());
        ServiceLocator::GetDatabase()->Execute(new AddGroupReservationLimitsCommand($groupIds));

    }

    private function GetReminderPieces($reminder)
    {
        if (!empty($reminder)) {
            $parts = explode(' ', strtolower($reminder));

            if (count($parts) == 2) {
                $interval = trim($parts[1]);
                $pieces['value'] = intval($parts[0]);
                $pieces['interval'] = ($interval == 'minutes' || $interval == 'hours' || $interval == 'days') ? $interval : 'minutes';
                return $pieces;
            }

            if (count($parts) == 1 && is_numeric($parts[0])) {
                $pieces['value'] = intval($parts[0]);
                $pieces['interval'] = 'minutes';
                return $pieces;
            }
        }

        return ['value' => null, 'interval' => 'minutes'];
    }

    private function AsBoolString($val, $invert = false)
    {
        $converted = BooleanConverter::ConvertValue($val);

        if ($invert) {
            return !$converted ? 'true' : 'false';
        }
        return $converted ? 'true' : 'false';
    }

    private function AsIntString($val)
    {
        if ($val == '') {
            return '';
        }

        $converted = intval($val);

        return $converted . '';

    }
}