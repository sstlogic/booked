<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

interface IReservationAuthorizationView
{
    /**
     * @return int
     */
    public function OwnerId();

    /**
     * @return IResource[]
     */
    public function Resources();

    /**
     * @return Date
     */
    public function StartDate();

    /**
     * @return Date
     */
    public function EndDate();

    /**
     * @return bool
     */
    public function RequiresApproval();

    /**
     * return int[]
     */
    public function CoOwnerIds();
}

class ReservationAuthorizationView implements IReservationAuthorizationView
{
    private $ownerId;
    private $startDate;
    private $endDate;
    private $resources;
    private $requiresApproval;
    private $coOwnerIds;

    /**
     * @param int $ownerId
     * @param Date $startDate
     * @param Date $endDate
     * @param IResource[] $resources
     * @param bool $requiresApproval
     * @param int[] $coOwnerIds
     */
    public function __construct($ownerId, $startDate, $endDate, $resources, $requiresApproval, $coOwnerIds)
    {
        $this->ownerId = $ownerId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->resources = $resources;
        $this->requiresApproval = $requiresApproval;
        $this->coOwnerIds = $coOwnerIds;
    }

    public static function ConvertView(ReservationView $view): ReservationAuthorizationView
    {
        return new ReservationAuthorizationView($view->OwnerId, $view->StartDate, $view->EndDate, $view->Resources, $view->RequiresApproval(), array_map(function ($co) { return $co->UserId;}, $view->CoOwners));
    }

    public static function ConvertSeries(ReservationSeries $series): ReservationAuthorizationView
    {
        return new ReservationAuthorizationView($series->UserId(), $series->CurrentInstance()->StartDate(), $series->CurrentInstance()->EndDate(), $series->AllResources(), $series->RequiresApproval(), $series->CurrentInstance()->CoOwners());
    }

    public function OwnerId()
    {
        return $this->ownerId;
    }

    public function Resources()
    {
        return $this->resources;
    }

    public function StartDate()
    {
        return $this->startDate;
    }

    public function EndDate()
    {
        return $this->endDate;
    }

    public function RequiresApproval()
    {
        return $this->requiresApproval;
    }

    public function CoOwnerIds()
    {
        return $this->coOwnerIds;
    }
}

interface IReservationAuthorization
{
    /**
     * @param UserSession $currentUser
     * @return bool
     */
    public function CanChangeUsers(UserSession $currentUser);

    /**
     * @param IReservationAuthorizationView $reservationView
     * @param UserSession $currentUser
     * @return bool
     */
    public function CanEdit(IReservationAuthorizationView $reservationView, UserSession $currentUser, $ignoreTimeConstraint = false);

    /**
     * @param IReservationAuthorizationView $reservationView
     * @param UserSession $currentUser
     * @return bool
     */
    public function CanApprove(IReservationAuthorizationView $reservationView, UserSession $currentUser);

    /**
     * @param IReservationAuthorizationView $reservationView
     * @param UserSession $currentUser
     * @return bool
     */
    public function CanViewDetails(IReservationAuthorizationView $reservationView, UserSession $currentUser);

    /**
     * @param IReservationAuthorizationView $reservationView
     * @param UserSession $currentUser
     * @return bool
     */
    public function IsAdmin(IReservationAuthorizationView $reservationView, UserSession $currentUser);

    /**
     * @param ReservationAuthorizationView $authView
     * @param UserSession $user
     * @return bool
     */
    public function IsTimeAccessible(ReservationAuthorizationView $authView, UserSession $user);
}

class ReservationAuthorization implements IReservationAuthorization
{
    /**
     * @var \IAuthorizationService
     */
    private $authorizationService;

    public function __construct(IAuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    public function CanEdit(IReservationAuthorizationView $reservationView, UserSession $currentUser, $ignoreTimeConstraint = false)
    {
        if ($currentUser->IsAdmin) {
            return true;
        }

        $adminForUser = $this->authorizationService->IsAdminFor($currentUser, $reservationView->OwnerId());
        $adminForResource = false;
        foreach ($reservationView->Resources() as $resource) {
            if ($this->authorizationService->CanEditForResource($currentUser, $resource)) {
                $adminForResource = true;
            }
        }

        if ($adminForUser || $adminForResource) {
            return true;
        }

        if ($ignoreTimeConstraint) {
            return $this->IsAccessibleTo($reservationView, $currentUser);
        }

        $ongoingReservation = true;
        $startTimeConstraint = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_TIME_CONSTRAINT);

        if ($startTimeConstraint == ReservationStartTimeConstraint::CURRENT) {
            $ongoingReservation = Date::Now()->LessThan($reservationView->EndDate());
        }

        if ($startTimeConstraint == ReservationStartTimeConstraint::FUTURE) {
            $ongoingReservation = Date::Now()->LessThan($reservationView->StartDate());
        }

        if ($ongoingReservation) {
            if ($this->IsAccessibleTo($reservationView, $currentUser)) {
                return true;
            }
        }

        return $currentUser->IsAdmin;    // only admins can edit reservations that have ended
    }

    public function CanChangeUsers(UserSession $currentUser)
    {
        return $currentUser->IsAdmin || $this->authorizationService->CanReserveForOthers($currentUser);
    }

    public function CanApprove(IReservationAuthorizationView $reservationView, UserSession $currentUser)
    {
        if (!$reservationView->RequiresApproval()) {
            return false;
        }

        if ($currentUser->IsAdmin) {
            return true;
        }

        $canReserveForUser = $this->authorizationService->CanApproveFor($currentUser, $reservationView->OwnerId());
        if ($canReserveForUser) {
            return true;
        }

        foreach ($reservationView->Resources() as $resource) {
            if ($this->authorizationService->CanApproveForResource($currentUser, $resource)) {
                return true;
            }
        }

        return false;
    }

    public function CanViewDetails(IReservationAuthorizationView $reservationView, UserSession $currentUser)
    {
        return $this->IsAccessibleTo($reservationView, $currentUser);
    }

    /**
     * @param IReservationAuthorizationView $reservationView
     * @param UserSession $currentUser
     * @return bool
     */
    private function IsAccessibleTo(IReservationAuthorizationView $reservationView, UserSession $currentUser)
    {
        if ($reservationView->OwnerId() == $currentUser->UserId || $currentUser->IsAdmin || in_array($currentUser->UserId, $reservationView->CoOwnerIds())) {
            return true;
        } else {
            $canReserveForUser = $this->authorizationService->CanReserveFor($currentUser, $reservationView->OwnerId());
            if ($canReserveForUser) {
                return true;
            }

            foreach ($reservationView->Resources() as $resource) {
                if ($this->authorizationService->CanEditForResource($currentUser, $resource)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function IsAdmin(IReservationAuthorizationView $reservationView, UserSession $currentUser)
    {
        if ($currentUser->IsAdmin) {
            return true;
        }

        $adminForUser = $this->authorizationService->IsAdminFor($currentUser, $reservationView->OwnerId());
        if ($adminForUser) {
            return true;
        }

        foreach ($reservationView->Resources() as $resource) {
            if ($this->authorizationService->CanEditForResource($currentUser, $resource)) {
                return true;
            }
        }

        return false;
    }

    public function IsTimeAccessible(ReservationAuthorizationView $reservationView, UserSession $currentUser)
    {
        $ongoingReservation = true;
        $startTimeConstraint = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_TIME_CONSTRAINT);

        if ($startTimeConstraint == ReservationStartTimeConstraint::CURRENT) {
            $ongoingReservation = Date::Now()->LessThan($reservationView->EndDate());
        }

        if ($startTimeConstraint == ReservationStartTimeConstraint::FUTURE) {
            $ongoingReservation = Date::Now()->LessThan($reservationView->StartDate());
        }

        if ($ongoingReservation) {
            if ($this->IsAccessibleTo($reservationView, $currentUser)) {
                return true;
            }
        }

        return $this->IsAdmin($reservationView, $currentUser);
    }
}