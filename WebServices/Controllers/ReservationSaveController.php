<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/Api/ReservationApiPresenter.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'WebServices/Requests/ReservationRequest.php');

interface IReservationSaveController
{
    /**
     * @param ReservationRequest $request
     * @param WebServiceUserSession $session
     * @return ReservationControllerResult
     */
    public function Create($request, WebServiceUserSession $session);

    /**
     * @param ReservationRequest $request
     * @param WebServiceUserSession $session
     * @param string $referenceNumber
     * @param string $updateScope
     * @return ReservationControllerResult
     */
    public function Update($request, $session, $referenceNumber, $updateScope);

    /**
     * @param WebServiceUserSession $session
     * @param string $referenceNumber
     * @return ReservationControllerResult
     */
    public function Approve($session, $referenceNumber);

    /**
     * @param WebServiceUserSession $session
     * @param string $referenceNumber
     * @param string $updateScope
     * @return ReservationControllerResult
     */
    public function Delete($session, $referenceNumber, $updateScope);

    /**
     * @param WebServiceUserSession $session
     * @param string $referenceNumber
     * @return ReservationControllerResult
     */
    public function Checkin($session, $referenceNumber);

    /**
     * @param WebServiceUserSession $session
     * @param string $referenceNumber
     * @return ReservationControllerResult
     */
    public function Checkout($session, $referenceNumber);
}

class ReservationSaveController implements IReservationSaveController
{
    /**
     * @var IReservationApiController
     */
    private $controller;

    public function __construct(IReservationApiController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param ReservationRequest $request
     */
    private function RequestToDto($request): ReservationApiDto
    {
        $dto = new ReservationApiDto();
        $dto->resourceIds = array_map('intval', array_values(array_unique(array_merge([$request->resourceId], ($request->resources ?? [])))));
        $dto->start = $request->startDateTime;
        $dto->end = $request->endDateTime;
        $dto->allowSelfJoin = intval($request->allowParticipation);
        foreach (($request->customAttributes ?? []) as $ca) {
            $dto->attributeValues[] = AttributeValueApiDto::Create(intval($ca->attributeId), apiencode($ca->attributeValue));
        }
        foreach (($request->accessories ?? []) as $a) {
            $dto->accessories = ReservationAccessoryApiDto::Create(intval($a->accessoryId), apiencode($a->quantityRequested));
        }
        $dto->description = apiencode($request->description);
        $dto->inviteeIds = array_map('intval', $request->invitees ?? []);
        $dto->participantIds = array_map('intval', $request->participants ?? []);
        $dto->coOwnerIds = array_map('intval', $request->coOwners ?? []);
        $dto->participantEmails = apiencode($request->participatingGuests ?? []);
        $dto->guestEmails = apiencode($request->invitedGuests ?? []);
        $dto->recurrence = ReservationRecurrenceApiDto::None();
        if (!empty($request->recurrenceRule)) {
            $dto->recurrence->type = $request->recurrenceRule->type;
            $dto->recurrence->interval = intval($request->recurrenceRule->interval);
            $dto->recurrence->terminationDate = $request->recurrenceRule->repeatTerminationDate;
            $dto->recurrence->monthlyType = $request->recurrenceRule->monthlyType;
            $dto->recurrence->weekdays = array_map('intval', $request->recurrenceRule->weekdays ?? []);
            $dto->recurrence->repeatDates = $request->recurrenceRule->repeatDates ?? [];
        }
        $dto->title = apiencode($request->title);
        $dto->ownerId = intval($request->userId);
        $dto->startReminder = empty($request->startReminder) ? null : ReservationReminderApiDto::Create($request->startReminder->interval, $request->startReminder->value);
        $dto->endReminder = empty($request->endReminder) ? null : ReservationReminderApiDto::Create($request->endReminder->interval, $request->endReminder->value);
        $dto->termsAcceptedDate = ($request->termsAccepted ?? false) ? Date::Now()->ToIso(true) : null;
        return $dto;
    }

    public function Create($request, WebServiceUserSession $session)
    {
        $this->controller->SetUser($session);
        $reservation = $this->RequestToDto($request);
        $validationErrors = $this->ValidateRequest($reservation);

        if (count($validationErrors) > 0) {
            return new ReservationControllerResult(null, $validationErrors);
        }

        $dto = new ReservationSaveRequestApiDto();
        $dto->reservation = $reservation;

        $result = $this->controller->CreateReservation($dto);

        /** @var ReservationSaveResponseApiDto $data */
        $data = $result->data;

        return new ReservationControllerResult($data->referenceNumber, $data->errors, $data->requiresApproval);
    }

    public function Update($request, $session, $referenceNumber, $updateScope)
    {
        $this->controller->SetUser($session);
        $reservation = $this->RequestToDto($request);
        $reservation->referenceNumber = $referenceNumber;

        $validationErrors = $this->ValidateUpdateRequest($reservation, $updateScope);

        if (count($validationErrors) > 0) {
            return new ReservationControllerResult(null, $validationErrors);
        }

        $dto = new ReservationSaveRequestApiDto();
        $dto->updateScope = $updateScope;
        $dto->reservation = $reservation;

        $result = $this->controller->UpdateReservation($dto);

        /** @var ReservationSaveResponseApiDto $data */
        $data = $result->data;

        return new ReservationControllerResult($data->referenceNumber, $data->errors, $data->requiresApproval);
    }

    /**
     * @param WebServiceUserSession $session
     * @param string $referenceNumber
     * @return ReservationControllerResult
     */
    public function Approve($session, $referenceNumber)
    {
        $this->controller->SetUser($session);
        $request = new ReservationReferenceNumberApiDto();
        $request->referenceNumber = $referenceNumber;

        $result = $this->controller->Approve($request);

        /** @var ReservationSaveResponseApiDto $data */
        $data = $result->data;

        return new ReservationControllerResult($data->referenceNumber, $data->errors, $data->requiresApproval);
    }

    /**
     * @param WebServiceUserSession $session
     * @param string $referenceNumber
     * @return ReservationControllerResult
     */
    public function Checkin($session, $referenceNumber)
    {
        $this->controller->SetUser($session);
        $request = new ReservationReferenceNumberApiDto();
        $request->referenceNumber = $referenceNumber;

        $result = $this->controller->CheckIn($request);

        /** @var ReservationSaveResponseApiDto $data */
        $data = $result->data;

        return new ReservationControllerResult($data->referenceNumber, $data->errors, $data->requiresApproval);
    }

    /**
     * @param WebServiceUserSession $session
     * @param string $referenceNumber
     * @return ReservationControllerResult
     */
    public function Checkout($session, $referenceNumber)
    {
        $this->controller->SetUser($session);
        $request = new ReservationReferenceNumberApiDto();
        $request->referenceNumber = $referenceNumber;

        $result = $this->controller->CheckOut($request);

        /** @var ReservationSaveResponseApiDto $data */
        $data = $result->data;

        return new ReservationControllerResult($data->referenceNumber, $data->errors, $data->requiresApproval);
    }

    public function Delete($session, $referenceNumber, $updateScope)
    {
        $this->controller->SetUser($session);
        $validationErrors = $this->ValidateDeleteRequest($referenceNumber, $updateScope);

        if (count($validationErrors) > 0) {
            return new ReservationControllerResult(null, $validationErrors);
        }

        $request = new ReservationDeleteRequestApiDto();
        $request->scope = $updateScope;
        $request->referenceNumber = $referenceNumber;

        $result = $this->controller->DeleteReservation($request);

        /** @var ReservationSaveResponseApiDto $data */
        $data = $result->data;

        return new ReservationControllerResult($data->referenceNumber, $data->errors, $data->requiresApproval);
    }

    /**
     * @param ReservationApiDto $request
     * @return array|string[]
     */
    private function ValidateRequest($request)
    {
        $errors = array();

        try {
            if (empty($request->resourceIds)) {
                $errors[] = 'Missing or invalid resourceId';
            }

            $startDate = $request->start;
            if (empty($startDate)) {
                $errors[] = 'Missing or invalid startDateTime';
            }

            $endDate = $request->end;
            if (empty($endDate)) {
                $errors[] = 'Missing or invalid endDateTime';
            }

            $repeatType = $request->recurrence->type;
            if (!empty($repeatType) && !RepeatType::IsDefined($repeatType)) {
                $errors[] = 'Invalid repeat type';
            }

            if ($repeatType == RepeatType::Monthly && !RepeatMonthlyType::IsDefined($request->recurrence->monthlyType)) {
                $errors[] = 'Missing or invalid repeatMonthlyType';
            }

            if (!empty($repeatType) && $repeatType != RepeatType::None) {
                $repeatInterval = $request->recurrence->interval;
                if (empty($repeatInterval)) {
                    $errors[] = 'Missing or invalid repeatInterval';
                }

                $repeatTerminationDate = $request->recurrence->terminationDate;
                if (empty($repeatTerminationDate)) {
                    $errors[] = 'Missing or invalid repeatTerminationDate';
                }
            }

            $accessories = $request->accessories;
            if (!empty($accessories)) {
                /** @var AccessoryFormElement $accessory */
                foreach ($accessories as $accessory) {
                    if (empty($accessory->Id) || empty($accessory->Quantity) || $accessory->Quantity < 0) {
                        $errors[] = 'Invalid accessory';
                    }
                }
            }
        } catch (Exception $ex) {
            $errors[] = 'Could not process request.' . $ex;
        }

        return $errors;
    }

    /**
     * @param ReservationApiDto $request
     * @param string|SeriesUpdateScope $updateScope
     * @return array|string[]
     */
    private function ValidateUpdateRequest($request, $updateScope)
    {
        return array_merge($this->ValidateRequest($request),
            $this->ValidateParams($request->referenceNumber, $updateScope));
    }

    /**
     * @param string $referenceNumber
     * @param string $updateScope
     * @return array|string[]
     */
    private function ValidateDeleteRequest($referenceNumber, $updateScope)
    {
        return $this->ValidateParams($referenceNumber, $updateScope);
    }

    /**
     * @param string $referenceNumber
     * @param string $updateScope
     * @return array|string[]
     */
    private function ValidateParams($referenceNumber, $updateScope)
    {
        $errors = array();

        if (empty($referenceNumber)) {
            $errors[] = "Missing or invalid referenceNumber: $referenceNumber";
        }

        if (!SeriesUpdateScope::IsValid($updateScope)) {
            $errors[] = "Missing or invalid updateScope: $updateScope";
        }

        return $errors;
    }
}

class ReservationControllerResult
{
    /**
     * @var string
     */
    private $createdReferenceNumber;

    /**
     * @var array|string[]
     */
    private $errors = array();
    /**
     * @var bool
     */
    private ?bool $requiresApproval;

    public function __construct($referenceNumber = null, $errors = array(), $requiresApproval = false)
    {
        $this->createdReferenceNumber = $referenceNumber;
        $this->errors = $errors;
        $this->requiresApproval = $requiresApproval;
    }

    /**
     * @param string $referenceNumber
     */
    public function SetReferenceNumber($referenceNumber)
    {
        $this->createdReferenceNumber = $referenceNumber;
    }

    /**
     * @return bool
     */
    public function WasSuccessful()
    {
        return !empty($this->createdReferenceNumber) && count($this->errors) == 0;
    }

    /**
     * @return string
     */
    public function CreatedReferenceNumber()
    {
        return $this->createdReferenceNumber;
    }

    /**
     * @return array|string[]
     */
    public function Errors()
    {
        return $this->errors;
    }

    /**
     * @param array|string[] $errors
     */
    public function SetErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function RequiresApproval()
    {
        return $this->requiresApproval;
    }

    /**
     * @param bool $requiresApproval
     */
    public function SetRequiresApproval($requiresApproval)
    {
        $this->requiresApproval = $requiresApproval;
    }
}