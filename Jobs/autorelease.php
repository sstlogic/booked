<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');

class AutoReleaseJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('auto-release', 1);
    }

    protected function Execute()
    {
        $reservationViewRepository = new ReservationViewRepository();
        $resourceRepository = new ResourceRepository();
        $reservationRepository = new ReservationRepository();

        $onlyAutoReleasedResources = new SqlFilterFreeForm(sprintf("`%s` IS NOT NULL AND `%s` <> ''",
            ColumnNames::AUTO_RELEASE_MINUTES, ColumnNames::AUTO_RELEASE_MINUTES));
        $autoReleasedResources = $resourceRepository->GetList(null, null, null, null, $onlyAutoReleasedResources)->Results();

        $userSession = new UserSession(0);
        $userSession->FirstName = 'Auto release job';
        $userSession->IsAdmin = true;

        /** @var BookableResource $resource */
        foreach ($autoReleasedResources as $resource) {
            $autoReleaseMinutes = $resource->GetAutoReleaseMinutes();

            $latestStartDate = Date::Now()->SubtractMinutes($autoReleaseMinutes)->ToDatabase();

            $reservationsThatShouldHaveBeenCheckedIn = new SqlFilterFreeForm(sprintf("`%s`.`%s` = %s AND `%s` IS NULL AND `%s`.`%s` < '%s' AND `%s`.`%s` > '%s'",
                TableNames::RESOURCES, ColumnNames::RESOURCE_ID, $resource->GetId(),
                ColumnNames::CHECKIN_DATE,
                TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_START, $latestStartDate,
                TableNames::RESERVATION_INSTANCES_ALIAS, ColumnNames::RESERVATION_END, Date::Now()->ToDatabase(),
            ));
            $reservationItemViews = $reservationViewRepository->GetList(null, null, null, null, $reservationsThatShouldHaveBeenCheckedIn)->Results();

            /** @var ReservationItemView $reservationItemView */
            foreach ($reservationItemViews as $reservationItemView) {
                Log::Debug('Automatically releasing reservation.',
                    ['referenceNumber' => $reservationItemView->ReferenceNumber,
                        'ownerFirstName' => $reservationItemView->OwnerFirstName,
                        'ownerLastName' => $reservationItemView->OwnerLastName,
                        'resourceName' => $reservationItemView->ResourceName,
                        'action' => $resource->GetAutoReleaseAction()
                    ]);

                $reservation = $reservationRepository->LoadByReferenceNumber($reservationItemView->ReferenceNumber);
                $reservation->ApplyChangesTo(SeriesUpdateScope::ThisInstance);

                $this->ReleaseReservation($userSession, $reservation, $resource, $reservationRepository);
            }
        }
    }

    private function ReleaseReservation(UserSession $userSession, ExistingReservationSeries $reservation, BookableResource $resource, IReservationRepository $reservationRepository)
    {
        if ($resource->GetAutoReleaseAction() == ResourceAutoReleaseAction::Delete) {
            $reservation->Delete($userSession);
            $reservationRepository->Delete($reservation);
        }

        if ($resource->GetAutoReleaseAction() == ResourceAutoReleaseAction::End) {
            $start = $reservation->CurrentInstance()->StartDate();
            $end = Date::Now()->ToTimezone($start->Timezone());
            $reservation->UpdateBookedBy($userSession);
            $reservation->UpdateDuration(new DateRange($start, $end));
            $reservationRepository->Update($reservation);
        }
    }
}

$autoReleaseJob = new AutoReleaseJob();
$autoReleaseJob->Run();