<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/namespace.php');
require_once(ROOT_DIR . 'WebServices/Responses/ReservationsResponse.php');
require_once(ROOT_DIR . 'WebServices/Responses/ReservationResponse.php');

class ReservationsWebService
{
    /**
     * @var IRestServer
     */
    private $server;

    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;

    /**
     * @var IPrivacyFilter
     */
    private $privacyFilter;

    /**
     * @var IAttributeService
     */
    private $attributeService;

    public function __construct(IRestServer                $server,
                                IReservationViewRepository $reservationViewRepository,
                                IPrivacyFilter             $privacyFilter,
                                IAttributeService          $attributeService)
    {
        $this->server = $server;
        $this->reservationViewRepository = $reservationViewRepository;
        $this->attributeService = $attributeService;
        $this->privacyFilter = $privacyFilter;
    }

    /**
     * @name GetReservations
     * @description Gets a list of reservations for the specified parameters.
     * Optional query string parameters: userId, resourceId, scheduleId, startDateTime, endDateTime.
     * If no dates are provided, reservations for the next two weeks will be returned.
     * If dates do not include the timezone offset, the timezone of the authenticated user will be assumed.
     * @response ReservationsResponse
     * @return Response
     */
    public function GetReservations(Request $request, Response $response, $args)
    {
        $startDate = $this->GetStartDate($request);
        $endDate = $this->GetEndDate($request);
        $userId = $this->GetUserId($request);
        $resourceId = $this->GetResourceId($request);
        $scheduleId = $this->GetScheduleId($request);

        Log::Debug('GetReservations called.', ['userId' => $userId, 'startDate' => $startDate, 'endDate' => $endDate]);

        $reservations = $this->reservationViewRepository->GetReservations($startDate, $endDate, $userId, null,
            $scheduleId, $resourceId);

        $responseObj = new ReservationsResponse($this->server, $reservations, $this->privacyFilter, $startDate, $endDate);
        return $this->server->WriteResponse($responseObj, $response);
    }

    /**
     * @name GetReservation
     * @param string $referenceNumber
     * @description Loads a specific reservation by reference number
     * @response ReservationResponse
     * @return Response
     */
    public function GetReservation(Request $request, Response $response, $args)
    {
        $referenceNumber = $args['referenceNumber'];
        Log::Debug('GetReservation called.', ['referenceNumber' => $referenceNumber]);

        $reservation = $this->reservationViewRepository->GetReservationForEditing($referenceNumber);

        if (!empty($reservation->ReferenceNumber)) {
            $attributes = $this->attributeService->GetAttributes(CustomAttributeCategory::RESERVATION, $this->server->GetSession());
            $responseObj = new ReservationResponse($this->server, $reservation, $this->privacyFilter, $attributes);
            return $this->server->WriteResponse($responseObj, $response);
        } else {
            return $this->server->WriteResponse(RestResponse::NotFound(), $response, RestResponse::NOT_FOUND_CODE);
        }
    }

    /**
     * @param int|null $userId
     * @param int|null $resourceId
     * @param int|null $scheduleId
     * @return bool
     */
    private function FilterProvided($userId, $resourceId, $scheduleId)
    {
        return !empty($userId) || !empty($resourceId) || !empty($scheduleId);
    }

    /**
     * @return Date
     */
    private function GetStartDate(Request $request)
    {
        return $this->GetBaseDate($request, WebServiceQueryStringKeys::START_DATE_TIME);
    }

    /**
     * @return Date
     */
    private function GetEndDate(Request $request)
    {
        return $this->GetBaseDate($request, WebServiceQueryStringKeys::END_DATE_TIME, 14);
    }

    /**
     * @param string $queryStringKey
     * @return Date
     */
    private function GetBaseDate(Request $request, $queryStringKey, $defaultNumberOfDays = 0)
    {
        $dateQueryString = $this->server->GetQueryString($request, $queryStringKey);
        if (empty($dateQueryString)) {
            return Date::Now()->AddDays($defaultNumberOfDays);
        }

        return WebServiceDate::GetDate($dateQueryString, $this->server->GetSession());
    }

    /**
     * @return int|null
     */
    private function GetUserId(Request $request)
    {
        $userIdQueryString = $this->server->GetQueryString($request, WebServiceQueryStringKeys::USER_ID);
        if (empty($userIdQueryString)) {
            return null;
        }

        return $userIdQueryString;
    }

    /**
     * @return int|null
     */
    private function GetResourceId(Request $request)
    {
        return $this->server->GetQueryString($request, WebServiceQueryStringKeys::RESOURCE_ID);
    }

    /**
     * @return int|null
     */
    private function GetScheduleId(Request $request)
    {
        return $this->server->GetQueryString($request, WebServiceQueryStringKeys::SCHEDULE_ID);
    }
}