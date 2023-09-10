<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once ROOT_DIR . 'Domain/Access/namespace.php';
require_once(ROOT_DIR . 'lib/Application/Reservation/Validation/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/namespace.php');

interface IParticipationHandler
{
    /**
     * @param string $referenceNumber
     * @param bool $fullSeries
     * @return ParticipationHandlerResult
     */
    public function HandleAccept($referenceNumber, $fullSeries): ParticipationHandlerResult;

    /**
     * @param string $referenceNumber
     * @param bool $fullSeries
     * @return ParticipationHandlerResult
     */
    public function HandleJoin($referenceNumber, $fullSeries): ParticipationHandlerResult;

    /**
     * @param string $referenceNumber
     * @param string $guestEmail
     * @param bool $fullSeries
     * @return ParticipationHandlerResult
     */
    public function HandleJoinGuest($referenceNumber, $guestEmail, $fullSeries): ParticipationHandlerResult;

    /**
     * @param string $referenceNumber
     * @param bool $fullSeries
     * @return ParticipationHandlerResult
     */
    public function HandleDecline($referenceNumber, $fullSeries): ParticipationHandlerResult;

    /**
     * @param string $referenceNumber
     * @param bool $fullSeries
     * @return ParticipationHandlerResult
     */
    public function HandleCancel($referenceNumber, $fullSeries): ParticipationHandlerResult;
}

class ParticipationHandlerResult
{
    /**
     * @var bool
     */
    public $success;
    /**
     * @var array|string[]
     */
    public $errors = [];

    public function __construct($success = true, $errors = [])
    {
        $this->success = $success;
        $this->errors = $errors;
    }
}

class ParticipationHandler implements IParticipationHandler
{
    /**
     * @var IReservationRepository
     */
    private $reservationRepository;
    /**
     * @var IParticipationNotification
     */
    private $notification;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;
    /**
     * @var UserSession
     */
    private $user;

    public function __construct(IReservationRepository $reservationRepository, IParticipationNotification $notification, IScheduleRepository $scheduleRepository, UserSession $user)
    {
        $this->reservationRepository = $reservationRepository;
        $this->notification = $notification;
        $this->scheduleRepository = $scheduleRepository;
        $this->user = $user;
    }

    public static function Create(UserSession $user)
    {
        return new ParticipationHandler(new ReservationRepository(), new ParticipationNotification(new UserRepository()), new ScheduleRepository(), $user);
    }

    public function HandleAccept($referenceNumber, $fullSeries): ParticipationHandlerResult
    {
        Log::Debug('Accepting invitation', ['referenceNumber' => $referenceNumber]);

        $series = $this->reservationRepository->LoadByReferenceNumber($referenceNumber);

        $ruleErrors = $this->CheckRules($series);

        if (!empty($ruleErrors)) {
            Log::Error('Could not accept invite', ['referenceNumber' => $referenceNumber]);
            return new ParticipationHandlerResult(false, $ruleErrors);
        }

        $capacityErrors = $this->CheckCapacity($series, $fullSeries);

        if (!empty($capacityErrors)) {
            Log::Error("Could not accept invite due to capacits", ['referenceNumber' => $referenceNumber]);
            return new ParticipationHandlerResult(false, $capacityErrors);
        }

        if ($fullSeries) {
            $accepted = $series->AcceptInvitationSeries($this->user->UserId);

            if (!$accepted) {
                Log::Error("Could not accept invite - not invited.", ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotJoinReservation")]);
            }
        } else {
            $accepted = $series->AcceptInvitation($this->user->UserId);
            if (!$accepted) {
                Log::Error("Could not accept invite - not invited.", ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotJoinReservation")]);
            }
        }

        $series->UpdateBookedBy($this->user);
        $this->reservationRepository->Update($series);
        $this->notification->Notify($series, $this->user->UserId, InvitationAction::Accept);

        return new ParticipationHandlerResult();
    }

    public function HandleJoin($referenceNumber, $fullSeries): ParticipationHandlerResult
    {
        Log::Debug('Joining invitation', ['referenceNumber' => $referenceNumber]);

        $series = $this->reservationRepository->LoadByReferenceNumber($referenceNumber);

        $ruleErrors = $this->CheckRules($series);

        if (!empty($ruleErrors)) {
            Log::Error('Could not join.', ['referenceNumber' => $referenceNumber]);
            return new ParticipationHandlerResult(false, $ruleErrors);
        }

        $capacityErrors = $this->CheckCapacity($series, $fullSeries);

        if (!empty($capacityErrors)) {
            Log::Error("Could not join due to capacity", ['referenceNumber' => $referenceNumber]);
            return new ParticipationHandlerResult(false, $capacityErrors);
        }

        if ($fullSeries) {
            $joined = $series->JoinReservationSeries($this->user->UserId);

            if (!$joined) {
                Log::Error("Could not join - already participating.", ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotJoinReservation")]);
            }

        } else {
            $joined = $series->JoinReservation($this->user->UserId);
            if (!$joined) {
                Log::Error("Could not join - already participating.", ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotJoinReservation")]);
            }
        }

        $series->UpdateBookedBy($this->user);

        $this->reservationRepository->Update($series);
        $this->notification->Notify($series, $this->user->UserId, InvitationAction::Join);

        return new ParticipationHandlerResult();
    }

    public function HandleJoinGuest($referenceNumber, $guestEmail, $fullSeries): ParticipationHandlerResult
    {
        Log::Debug('Joining invitation as guest.', ['referenceNumber' => $referenceNumber, 'guestEmail' => $guestEmail]);

        $series = $this->reservationRepository->LoadByReferenceNumber($referenceNumber);

        $ruleErrors = $this->CheckRules($series);

        if (!empty($ruleErrors)) {
            Log::Error('Could not join.', ['referenceNumber' => $referenceNumber]);
            return new ParticipationHandlerResult(false, $ruleErrors);
        }

        $capacityErrors = $this->CheckCapacity($series, $fullSeries);

        if (!empty($capacityErrors)) {
            Log::Error('Could not join due to capacity.', ['referenceNumber' => $referenceNumber]);
            return new ParticipationHandlerResult(false, $capacityErrors);
        }

        if ($fullSeries) {
            $joined = $series->JoinSeriesAsGuest($guestEmail);

            if (!$joined) {
                Log::Error("Could not join - already participating.", ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotJoinReservation")]);
            }
        } else {
            $joined = $series->JoinAsGuest($guestEmail);
            if (!$joined) {
                Log::Error("Could not join - already participating.", ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotJoinReservation")]);
            }
        }

        $series->UpdateBookedBy($this->user);
        $this->reservationRepository->Update($series);
        $this->notification->NotifyGuest($series, $guestEmail, InvitationAction::Join);

        return new ParticipationHandlerResult();
    }

    public function HandleDecline($referenceNumber, $fullSeries): ParticipationHandlerResult
    {
        Log::Debug('Declining invitation.', ['referenceNumber' => $referenceNumber]);

        $series = $this->reservationRepository->LoadByReferenceNumber($referenceNumber);

        $ruleErrors = $this->CheckRules($series);

        if (!empty($ruleErrors)) {
            Log::Error('Could not decline.', ['referenceNumber' => $referenceNumber]);
            return new ParticipationHandlerResult(false, $ruleErrors);
        }

        if ($fullSeries) {
            $declined = $series->DeclineInvitationSeries($this->user->UserId);
            if (!$declined) {
                Log::Error('Could not decline - not invited', ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotDeclineReservation")]);
            }
        } else {
            $declined = $series->DeclineInvitation($this->user->UserId);
            if (!$declined) {
                Log::Error('Could not decline - not invited.', ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotDeclineReservation")]);
            }
        }

        $series->UpdateBookedBy($this->user);
        $this->reservationRepository->Update($series);
        $this->notification->Notify($series, $this->user->UserId, InvitationAction::Decline);

        return new ParticipationHandlerResult();
    }

    public function HandleCancel($referenceNumber, $fullSeries): ParticipationHandlerResult
    {
        Log::Debug('Canceling participation', ['referenceNumber' => $referenceNumber]);

        $series = $this->reservationRepository->LoadByReferenceNumber($referenceNumber);

        $ruleErrors = $this->CheckRules($series);

        if (!empty($ruleErrors)) {
            Log::Error('Could not cancel participation', ['referenceNumber' => $referenceNumber]);
            return new ParticipationHandlerResult(false, $ruleErrors);
        }

        if ($fullSeries) {
            $cancelled = $series->CancelAllParticipation($this->user->UserId);

            if (!$cancelled) {
                Log::Error("Could not cancel - not participating.", ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotDeclineReservation")]);
            }
        } else {
            $cancelled = $series->CancelInstanceParticipation($this->user->UserId);
            if (!$cancelled) {
                Log::Error("Could not cancel - not participating.", ['referenceNumber' => $referenceNumber]);
                return new ParticipationHandlerResult(false, [Resources::GetInstance()->GetString("CannotDeclineReservation")]);
            }
        }

        $series->UpdateBookedBy($this->user);
        $this->reservationRepository->Update($series);
        $this->notification->Notify($series, $this->user->UserId, InvitationAction::CancelInstance);

        return new ParticipationHandlerResult();
    }

    /**
     * @param ExistingReservationSeries $series
     * @param bool $fullSeries
     * @return string|null
     */
    private function CheckCapacity(ExistingReservationSeries $series, $fullSeries)
    {
        $capacity = null;
        $resourceName = "";

        foreach ($series->AllResources() as $resource) {
            if (!$resource->HasMaxParticipants()) {
                continue;
            }

            if ($capacity == null) {
                $resourceName = $resource->GetName();
                $capacity = $resource->GetMaxParticipants();

            } else if ($resource->GetMaxParticipants() < $capacity) {
                $resourceName = $resource->GetName();
                $capacity = $resource->GetMaxParticipants();
            }
        }

        if (empty($capacity)) {
            return null;
        }

        if ($fullSeries) {
            foreach ($series->Instances() as $instance) {
                $numberOfParticipants = $instance->TotalParticipantCountIncludingOwner();

                if (($numberOfParticipants + 1) > $capacity) {
                    return Resources::GetInstance()->GetString('MaxParticipantsError', array($resourceName, $capacity));
                }
            }
        } else {
            $instance = $series->CurrentInstance();
            $numberOfParticipants = $instance->TotalParticipantCountIncludingOwner();

            if (($numberOfParticipants + 1) > $capacity) {
                return Resources::GetInstance()->GetString('MaxParticipantsError', array($resourceName, $capacity));
            }
        }

        return null;
    }

    /**
     * @param ExistingReservationSeries $series
     * @return string[]
     */
    private function CheckRules(ExistingReservationSeries $series)
    {
        /** @var IReservationValidationRule[] $rules */
        $rules = [new ReservationStartTimeRule($this->scheduleRepository), new ResourceMinimumNoticeRuleUpdate($this->user), new ResourceMaximumNoticeRule($this->user)];

        $errors = [];

        foreach ($rules as $rule) {
            $result = $rule->Validate($series, null);
            if (!$result->IsValid()) {
                $errors[] = $result->ErrorMessage();
            }
        }

        return $errors;
    }
}