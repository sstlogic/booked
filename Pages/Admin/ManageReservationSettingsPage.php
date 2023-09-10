<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageReservationSettingsPresenter.php');
require_once(ROOT_DIR . 'lib/Config/Configurator.php');

interface IManageReservationSettingsPage extends IActionPage
{
    public function SetRequireTitle($required);

    public function SetRequireDescription($required);

    public function SetRemindersEnabled($enabled);

    public function SetAllowGuests($allowed);

    public function SetAllowParticipation($allowed);

    public function SetAllowWaitlist($allowed);

    public function SetShowEmail($show);

    public function SetAllowRecurrence($allowed);

    public function SetShowDetailedSave($show);

    public function SetStartReminder($value, $interval);

    public function SetEndReminder($value, $interval);

    public function SetCheckinMinutes($minutes);

    public function SetMaxCheckboxes($maxCheckboxes);

    public function SetStartConstraint($constraint);

    public function SetRequireUpdateApproval($required);

    public function SetLimitInvitees($limit);

    public function SetAllowMeetingLinks($allowed);

    /**
     * @param $groups GroupItemView[]
     * @param $selectedGroupIds int[]
     */
    public function SetGroups($groups, $selectedGroupIds);

    public function GetTitleRequired();

    public function GetDescriptionRequired();

    public function GetRemindersEnabled();

    public function GetAllowGuests();

    public function GetAllowWaitlist();

    public function GetAllowParticipation();

    public function GetAllowRecurrence();

    public function GetShowDetailedSave();

    public function GetUpdatesRequireApproval();

    public function GetShowEmail();

    public function GetCheckinMinutes();

    public function GetCheckboxLimit();

    public function GetStartTimeConstraint();

    public function GetAllowMeetingLinks();

    public function IsStartReminderEnabled();

    public function GetStartReminderValue();

    public function GetStartReminderInterval();

    public function IsEndReminderEnabled();

    public function GetEndReminderValue();

    public function GetEndReminderInterval();

    /**
     * @return int[]
     */
    public function GetLimitedGroupIds();

    /**
     * @return bool
     */
    public function GetLimitInvites();
}

class ManageReservationSettingsPage extends ActionPage implements IManageReservationSettingsPage, IPageWithId
{
    /**
     * @var ManageReservationSettingsPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct("ManageReservationSettings", 1);

        $this->presenter = new ManageReservationSettingsPresenter($this, new Configurator(), new GroupRepository());
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessPageLoad()
    {
        $this->presenter->PageLoad();
        $this->Display('Admin/Reservations/manage_reservation_settings.tpl');
    }

    public function SetRequireTitle($required)
    {
        $this->Set('TitleRequired', $required);
    }

    public function SetRequireDescription($required)
    {
        $this->Set('DescriptionRequired', $required);
    }

    public function SetRemindersEnabled($enabled)
    {
        $this->Set('RemindersEnabled', $enabled);
    }

    public function SetAllowGuests($allowed)
    {
        $this->Set('AllowGuests', $allowed);
    }

    public function SetAllowParticipation($allowed)
    {
        $this->Set("AllowParticipation", $allowed);
    }

    public function SetAllowWaitlist($allowed)
    {
        $this->Set("AllowWaitlist", $allowed);
    }

    public function SetAllowRecurrence($allowed)
    {
        $this->Set("AllowRecurrence", $allowed);
    }

    public function SetShowDetailedSave($show)
    {
        $this->Set("ShowDetailedSave", $show);
    }

    public function SetStartReminder($value, $interval)
    {
        $this->Set("StartReminderValue", $value);
        $this->Set("StartReminderInterval", $interval);
    }

    public function SetEndReminder($value, $interval)
    {
        $this->Set("EndReminderValue", $value);
        $this->Set("EndReminderInterval", $interval);
    }

    public function SetCheckinMinutes($minutes)
    {
        $this->Set("CheckinMinutes", $minutes);
    }

    public function SetMaxCheckboxes($maxCheckboxes)
    {
        $this->Set('MaxCheckboxes', $maxCheckboxes);
    }

    public function SetStartConstraint($constraint)
    {
        $this->Set('StartConstraint', $constraint);
    }

    public function SetRequireUpdateApproval($required)
    {
        $this->Set('UpdatesRequireApproval', $required);
    }

    public function SetShowEmail($show)
    {
        $this->Set('ShowEmail', $show);
    }

    public function SetLimitInvitees($limit)
    {
        $this->Set('LimitInvitees', $limit);
    }

    public function SetGroups($groups, $selectedGroupIds)
    {
        $this->Set('Groups', $groups);
        $this->Set('SelectedGroupIds', $selectedGroupIds);
    }

    public function GetTitleRequired()
    {
        return $this->GetForm('require-title');
    }

    public function GetDescriptionRequired()
    {
        return $this->GetForm('require-description');
    }

    public function GetRemindersEnabled()
    {
        return $this->GetForm('allow-reminders');
    }

    public function GetAllowGuests()
    {
        return $this->GetForm('allow-guest');
    }

    public function GetAllowWaitlist()
    {
        return $this->GetForm('allow-waitlist');
    }

    public function GetAllowParticipation()
    {
        return $this->GetForm('allow-participants');
    }

    public function GetAllowRecurrence()
    {
        return $this->GetForm('allow-recurring');
    }

    public function GetShowDetailedSave()
    {
        return $this->GetForm('show-save-details');
    }

    public function GetUpdatesRequireApproval()
    {
        return $this->GetForm('updates-require-approval');
    }

    public function GetShowEmail()
    {
        return $this->GetForm('show-email');
    }

    public function GetCheckinMinutes()
    {
        return $this->GetForm('checkin-minutes');
    }

    public function GetCheckboxLimit()
    {
        return $this->GetForm('checkbox-limit');
    }

    public function GetStartTimeConstraint()
    {
        return $this->GetForm('start-time-constraint');
    }

    public function IsStartReminderEnabled()
    {
        return !$this->GetCheckbox('start-reminder-none');
    }

    public function GetStartReminderValue()
    {
        return $this->GetForm('start-reminder-value');
    }

    public function GetStartReminderInterval()
    {
        return $this->GetForm('start-reminder-interval');
    }

    public function IsEndReminderEnabled()
    {
        return !$this->GetCheckbox('end-reminder-none');
    }

    public function GetEndReminderValue()
    {
        return $this->GetForm('end-reminder-value');
    }

    public function GetEndReminderInterval()
    {
        return $this->GetForm('end-reminder-interval');
    }

    public function GetLimitedGroupIds()
    {
        return $this->GetForm('participant-groups');
    }

    public function GetLimitInvites()
    {
        return $this->GetForm('limit-invitees');
    }

    public function SetAllowMeetingLinks($allowed)
    {
        $this->Set('AllowMeetingLinks', $allowed);
    }

    public function GetAllowMeetingLinks()
    {
        return $this->GetForm('allow-meeting-links');
    }

    public function GetPageId(): int
    {
        return AdminPageIds::ReservationSettings;
    }
}