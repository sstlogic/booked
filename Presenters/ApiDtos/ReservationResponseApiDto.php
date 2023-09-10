<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class ReservationResponseApiDto
{
    /**
     * @var ReservationApiDto
     */
    public $reservation;
    /**
     * @var ResourceApiDto[]
     */
    public $resources = [];
    /**
     * @var ResourceGroupApiDto[]
     */
    public $resourceGroups = [];
    /**
     * @var AttributeApiDto[]
     */
    public $resourceAttributes = [];
    /**
     * @var AccessoryApiDto[]
     */
    public $accessories = [];
    /**
     * @var UserApiDto[]
     */
    public $users = [];
    /**
     * @var GroupApiDto[]
     */
    public $groups = [];
    /**
     * @var ReservationTermsApiDto|null
     */
    public $terms;
    /**
     * @var ReservationScheduleApiDto
     */
    public $schedule;
    /**
     * @var ReservationCheckinApiDto
     */
    public $checkin;
    /**
     * @var bool
     */
    public $canApprove;
    /**
     * @var bool
     */
    public $canEdit;
    /**
     * @var bool
     */
    public $canChangeUser;
    /**
     * @var bool
     */
    public $canView;
    /**
     * @var bool
     */
    public $isWaitlisted;
    /**
     * @var number
     */
    public $waitlistCount = 0;

    /**
     * @var ReservationMeetingConnectionsApiDto
     */
    public $meetingConnections;

    public function Censor(bool $canViewUser, bool $canView)
    {
        if (!$canView) {
            Log::Debug("Censoring reservation load response");
            $this->reservation->accessories = [];
            $this->reservation->attachments = [];
            $this->reservation->checkinDate = null;
            $this->reservation->checkoutDate = null;
            $this->reservation->description = null;
            $this->reservation->endReminder = null;
            $this->reservation->guestEmails = [];
            $this->reservation->ownerId = 0;
            $this->reservation->participantEmails = [];
            $this->reservation->inviteeIds = [];
            $this->reservation->participantIds = [];
            $this->reservation->startReminder = null;
            $this->reservation->title = null;
        }

        if (!$canViewUser) {
            Log::Debug("Hiding user in reservation load response");
            $this->reservation->ownerId = 0;
        }
    }
}
