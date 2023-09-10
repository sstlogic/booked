<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Email/Messages/CoOwnerAddedEmail.php');

class CoOwnerUpdatedEmailNotification extends CoOwnerAddedEmailNotification
{

    /**
     * @param ReservationSeries $reservationSeries
     */
    function Notify($reservationSeries)
    {
        $instance = $reservationSeries->CurrentInstance();
        $owner = $this->userRepository->LoadById($reservationSeries->UserId());

        foreach ($instance->AddedCoOwners() as $userId) {
            $coowner = $this->userRepository->LoadById($userId);

            $message = new CoOwnerAddedEmail($owner, $coowner, $reservationSeries, $this->attributeRepository, $this->userRepository);
            ServiceLocator::GetEmailService()->Send($message);
        }

        foreach ($instance->UnchangedCoOwners() as $userId) {
            $coowner = $this->userRepository->LoadById($userId);

            $message = new CoOwnerUpdatedEmail($owner, $coowner, $reservationSeries, $this->attributeRepository, $this->userRepository);
            ServiceLocator::GetEmailService()->Send($message);
        }

        foreach ($instance->RemovedCoOwners() as $userId) {
            $coowner = $this->userRepository->LoadById($userId);

            $message = new CoOwnerDeletedEmail($owner, $coowner, $reservationSeries, $this->attributeRepository, $this->userRepository);
            ServiceLocator::GetEmailService()->Send($message);
        }
    }
}