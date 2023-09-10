<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

interface IAdminForReservationCheck
{
    /**
     * @param ReservationSeries $reservationSeries
     * @return bool
     */
    public function Check($reservationSeries);
}

class AdminForReservationCheck implements IAdminForReservationCheck
{
    /**
     * @var UserSession
     */
    private $userSession;

    /**
     * @var IUserRepository
     */
    private $userRepository;

    public function __construct(UserSession $userSession, IUserRepository $userRepository)
    {
        $this->userSession = $userSession;
        $this->userRepository = $userRepository;
    }

    public function Check($reservationSeries)
    {
        if ($this->userSession->IsAdmin) {
            Log::Debug('User is application admin.', ['userId' => $this->userSession->UserId]);
            return true;
        }

        if ($this->userSession->IsGroupAdmin || $this->userSession->IsResourceAdmin || $this->userSession->IsScheduleAdmin) {
            if ($this->userSession->IsGroupAdmin) {
                $user = $this->userRepository->LoadById($this->userSession->UserId);
                $reservationUser = $this->userRepository->LoadById($reservationSeries->UserId());

                if ($user->IsAdminFor($reservationUser)) {
                    Log::Debug('User is admin for reservation user.', ['userId' => $this->userSession->UserId]);

                    return true;
                }
            }

            if ($this->userSession->IsResourceAdmin || $this->userSession->IsScheduleAdmin) {
                $user = $this->userRepository->LoadById($this->userSession->UserId);
                $isResourceAdmin = true;

                foreach ($reservationSeries->AllResources() as $resource) {
                    if (!$user->IsResourceAdminFor($resource)) {
                        $isResourceAdmin = false;
                        break;
                    }
                }

                if ($isResourceAdmin) {
                    Log::Debug('User is admin for all resources', ['userId' => $this->userSession->UserId]);

                    return true;
                }
            }
        }

        return false;
    }
}