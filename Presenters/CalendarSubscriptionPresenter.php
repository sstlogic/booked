<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 * Copyright 2012-2014 Alois Schloegl
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');

class CalendarSubscriptionPresenter
{
    /**
     * @var ICalendarExportPage
     */
    private $page;

    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;

    /**
     * @var ICalendarExportValidator
     */
    private $validator;

    /**
     * @var ICalendarSubscriptionService
     */
    private $subscriptionService;

    /**
     * @var IPrivacyFilter
     */
    private $privacyFilter;

    public function __construct(ICalendarSubscriptionPage    $page,
                                IReservationViewRepository   $reservationViewRepository,
                                ICalendarExportValidator     $validator,
                                ICalendarSubscriptionService $subscriptionService,
                                IPrivacyFilter               $filter)
    {
        $this->page = $page;
        $this->reservationViewRepository = $reservationViewRepository;
        $this->validator = $validator;
        $this->subscriptionService = $subscriptionService;
        $this->privacyFilter = $filter;
    }

    public function PageLoad()
    {
        if (!$this->validator->IsValid()) {
            return;
        }

        $userId = $this->page->GetUserId();
        $scheduleId = $this->page->GetScheduleId();
        $resourceId = $this->page->GetResourceId();
        $accessoryIds = $this->page->GetAccessoryIds();
        $resourceGroupId = $this->page->GetResourceGroupId();

        $daysAgo = $this->page->GetPastNumberOfDays();
        $daysAhead = $this->page->GetFutureNumberOfDays();

        $pastDays = Configuration::Instance()->GetSectionKey(ConfigSection::ICS, ConfigKeys::ICS_PAST_DAYS, new IntConverter());
        $futureDays = Configuration::Instance()->GetSectionKey(ConfigSection::ICS, ConfigKeys::ICS_FUTURE_DAYS, new IntConverter());
        if ($futureDays == 0) {
            $futureDays = 30;
        }

        $daysAgo = empty($daysAgo) ? $pastDays : intval($daysAgo);
        $daysAhead = empty($daysAhead) ? $futureDays : intval($daysAhead);

        $daysAgo = min($daysAgo, 30);
        $daysAhead = min($daysAhead, 90);

        $weekAgo = Date::Now()->AddDays(-$daysAgo);
        $nextYear = Date::Now()->AddDays($daysAhead);

        $sid = null;
        $rid = null;
        $uid = null;
        $aid = null;
        $resourceIds = array();

        $reservations = array();
        $res = array();

        $summaryFormat = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION_LABELS, ConfigKeys::RESERVATION_LABELS_ICS_SUMMARY);
        $descriptionFormat = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION_LABELS, ConfigKeys::RESERVATION_LABELS_ICS_DESCRIPTION);

        $reservationUserLevel = ReservationUserLevel::OWNER;
        if (!empty($scheduleId)) {
            $schedule = $this->subscriptionService->GetSchedule($scheduleId);
            $sid = $schedule->GetId();
        }
        if (!empty($resourceId)) {
            $resource = $this->subscriptionService->GetResource($resourceId);
            $rid = $resource->GetId();
        }
        if (!empty($accessoryIds)) {
            ## No transformation is implemented. It is assumed the accessoryIds is provided as AccessoryName
            ## filter is defined by LIKE "PATTERN%"
            $aid = $accessoryIds;
        }
        if (!empty($userId)) {
            $user = $this->subscriptionService->GetUser($userId);
            $uid = $user->Id();
            $reservationUserLevel = ReservationUserLevel::ALL;
            $summaryFormat = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION_LABELS, ConfigKeys::RESERVATION_LABELS_MY_ICS_SUMMARY);
            $descriptionFormat = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION_LABELS, ConfigKeys::RESERVATION_LABELS_MY_ICS_DESCRIPTION);
        }
        if (!empty($resourceGroupId)) {
            $resourceIds = $this->subscriptionService->GetResourcesInGroup($resourceGroupId);
        }

        if (!empty($uid) || !empty($sid) || !empty($rid) || !empty($resourceIds)) {
            $res = $this->reservationViewRepository->GetReservations($weekAgo, $nextYear, $uid, $reservationUserLevel, $sid, $rid, true);
        }

        Log::Debug('Loading calendar subscription.',
            ['userId' => $userId, 'scheduleId' => $scheduleId, 'resourceId' => $resourceId, 'reservationCount' => count($res)]);

        $session = new NullUserSession(true, false);

        foreach ($res as $r) {
            if (empty($resourceIds) || in_array($r->ResourceId, $resourceIds)) {
                $reservations[] = new iCalendarReservationView($r,
                    $session,
                    $this->privacyFilter,
                    $summaryFormat,
                    $descriptionFormat);
            }
        }

        $this->page->SetReservations($reservations);
    }
}