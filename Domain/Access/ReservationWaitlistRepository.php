<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/ReservationWaitlistRequest.php');

interface IReservationWaitlistRepository
{
    /**
     * @param ReservationWaitlistRequest $request
     * @return int
     */
    public function Add(ReservationWaitlistRequest $request);

    /**
     * @return ReservationWaitlistRequest[]
     */
    public function GetAll();

    /**
     * @param int $waitlistId
     * @return ReservationWaitlistRequest
     */
    public function LoadById($waitlistId);

    /**
     * @param ReservationWaitlistRequest $request
     */
    public function Delete(ReservationWaitlistRequest $request);

    /**
     * @param int $userId
     * @param int[] $resourceIds
     * @param Date $startDate
     * @param Date $endDate
     * @return ReservationWaitlistRequest
     */
    public function FindWaitlistRequest($userId, $resourceIds, Date $startDate, Date $endDate);

    /**
     * @param int[] $resourceIds
     * @param Date $startDate
     * @param Date $endDate
     * @return ReservationWaitlistRequest[]
     */
    public function FindWaitlistRequests($resourceIds, Date $startDate, Date $endDate);

    /**
     * @param int $userId
     * @return ReservationWaitlistRequest[]
     */
    public function FindUpcomingWaitlistRequests($userId);

    /**
     * @param int $userId
     * @param int[] $resourceIds
     * @param int $scheduleId
     * @param DateRange $dateRange
     * @return ReservationWaitlistRequest[]
     */
    public function Search($userId, $resourceIds, $scheduleId, DateRange $dateRange);
}

class ReservationWaitlistRepository implements IReservationWaitlistRepository
{
    /**
     * @param ReservationWaitlistRequest $request
     * @return int
     */
    public function Add(ReservationWaitlistRequest $request)
    {
        $command = new AddReservationWaitlistCommand($request->UserId(), $request->StartDate(), $request->EndDate(), $request->ResourceId());
        $id = ServiceLocator::GetDatabase()->ExecuteInsert($command);

        $request->WithId($id);

        return $id;
    }

    public function GetAll()
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllReservationWaitlistRequests());

        $requests = array();

        while ($row = $reader->GetRow()) {
            $requests[] = ReservationWaitlistRequest::FromRow($row);
        }

        $reader->Free();

        return $requests;
    }

    public function Delete(ReservationWaitlistRequest $request)
    {
        ServiceLocator::GetDatabase()->Execute(new DeleteReservationWaitlistCommand($request->Id()));
    }

    public function LoadById($waitlistId)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetReservationWaitlistRequestCommand($waitlistId));

        if ($row = $reader->GetRow()) {
            $reader->Free();
            return ReservationWaitlistRequest::FromRow($row);
        }

        return null;
    }

    public function FindWaitlistRequest($userId, $resourceIds, Date $startDate, Date $endDate)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetReservationWaitlistRequestForUserCommand($userId, $resourceIds, $startDate, $endDate));

        if ($row = $reader->GetRow()) {
            $reader->Free();
            return ReservationWaitlistRequest::FromRow($row);
        }

        return null;
    }

    public function FindWaitlistRequests($resourceIds, Date $startDate, Date $endDate)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetReservationWaitlistRequestsCommand($resourceIds, $startDate, $endDate));

        $requests = [];
        while ($row = $reader->GetRow()) {
            $requests[] = ReservationWaitlistRequest::FromRow($row);
        }

        $reader->Free();

        return $requests;
    }

    public function FindUpcomingWaitlistRequests($userId)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetReservationWaitlistUpcomingRequestsForUserCommand($userId, Date::Now()));

        $requests = [];
        while ($row = $reader->GetRow()) {
            $requests[] = ReservationWaitlistRequest::FromRow($row);
        }

        $reader->Free();

        return $requests;
    }

    public function Search($userId, $resourceIds, $scheduleId, DateRange $dateRange)
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetReservationWaitlistSearchCommand($userId, $resourceIds, $scheduleId, $dateRange));

        $requests = [];
        while ($row = $reader->GetRow()) {
            $requests[] = ReservationWaitlistRequestDto::FromRow($row);
        }

        $reader->Free();

        return $requests;
    }
}

class ReservationWaitlistRequestDto
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var Date
     */
    private $startDate;
    /**
     * @var Date
     */
    private $endDate;

    /**
     * @var int
     */
    private $resourceId;

    /**
     * @var string
     */
    private $resourceName;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $fullName;

    /**
     * @param array $row
     * @return ReservationWaitlistRequestDto
     */
    public static function FromRow($row)
    {
        $dto = new ReservationWaitlistRequestDto();
        $dto->id = $row[ColumnNames::RESERVATION_WAITLIST_REQUEST_ID];
        $dto->userId = $row[ColumnNames::USER_ID];
        $dto->startDate = Date::FromDatabase($row[ColumnNames::RESERVATION_START]);
        $dto->endDate = Date::FromDatabase($row[ColumnNames::RESERVATION_END]);
        $dto->resourceId = $row[ColumnNames::RESOURCE_ID];
        $dto->resourceName = $row[ColumnNames::RESOURCE_NAME];
        $dto->firstName = $row[ColumnNames::FIRST_NAME];
        $dto->lastName = $row[ColumnNames::LAST_NAME];
        $dto->fullName = FullName::AsString($dto->firstName, $dto->lastName);
        return $dto;
    }

    /**
     * @return int
     */
    public function Id()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function UserId()
    {
        return $this->userId;
    }

    /**
     * @return Date
     */
    public function StartDate()
    {
        return $this->startDate;
    }

    /**
     * @return Date
     */
    public function EndDate()
    {
        return $this->endDate;
    }

    /**
     * @return int
     */
    public function ResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @return string
     */
    public function ResourceName()
    {
        return $this->resourceName;
    }

    /**
     * @return string
     */
    public function UserName()
    {
        return $this->fullName;
    }
}