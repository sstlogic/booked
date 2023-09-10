<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once(ROOT_DIR . 'lib/WebService/namespace.php');
require_once(ROOT_DIR . 'WebServices/Controllers/ReservationSaveController.php');
require_once(ROOT_DIR . 'WebServices/Responses/ReservationCreatedResponse.php');
require_once(ROOT_DIR . 'WebServices/Requests/ReservationRequest.php');

class ReservationWriteWebService
{
    /**
     * @var IRestServer
     */
    private $server;

    /**
     * @var IReservationSaveController
     */
    private $controller;

    public function __construct(IRestServer $server, IReservationSaveController $controller)
    {
        $this->server = $server;
        $this->controller = $controller;
    }

    /**
     * @name CreateReservation
     * @description Creates a new reservation
     * @request ReservationRequest
     * @response ReservationCreatedResponse
     * @return Response
     */
    public function Create(Request $request, Response $response)
    {
        /** @var $req ReservationRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('ReservationWriteWebService.Create()', ['userId' => $this->server->GetSession()->UserId, 'request' => $req]);

        $result = $this->controller->Create($req, $this->server->GetSession());

        if ($result->WasSuccessful()) {
            Log::Debug('ReservationWriteWebService.Create() - Reservation Created.', ['referenceNumber' => $result->CreatedReferenceNumber()]);

            return $this->server->WriteResponse(new ReservationCreatedResponse($this->server, $result->CreatedReferenceNumber(), $result->RequiresApproval()),
                $response, RestResponse::CREATED_CODE);
        } else {
            Log::Debug('ReservationWriteWebService.Create() - Reservation Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response,
                RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name UpdateReservation
     * @description Updates an existing reservation.
     * Pass an optional updateScope query string parameter to restrict changes. Possible values for updateScope are this|full|future
     * @request ReservationRequest
     * @response ReservationUpdatedResponse
     * @param string $referenceNumber
     * @return Response
     */
    public function Update(Request $request, Response $response, $args)
    {
        $referenceNumber = $args['referenceNumber'];

        /** @var $req ReservationRequest */
        $req = $this->server->GetRequest($request);

        Log::Debug('ReservationWriteWebService.Update()', ['referenceNumber' => $referenceNumber,
            'userId' => $this->server->GetSession()->UserId,
            'request' => $req]);

        $updateScope = $this->server->GetQueryString($request, WebServiceQueryStringKeys::UPDATE_SCOPE);
        if (empty($updateScope)) {
            $updateScope = SeriesUpdateScope::ThisInstance;
        }
        $result = $this->controller->Update($req, $this->server->GetSession(), $referenceNumber, $updateScope);

        if ($result->WasSuccessful()) {
            Log::Debug('ReservationWriteWebService.Update() - Reservation Updated.',
                ['referenceNumber' => $result->CreatedReferenceNumber()]);

            return $this->server->WriteResponse(new ReservationUpdatedResponse($this->server, $result->CreatedReferenceNumber(), $result->RequiresApproval()),
                $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('ReservationWriteWebService.Update() - Reservation Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()),
                $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name ApproveReservation
     * @description Approves a pending reservation.
     * @response ReservationApprovedResponse
     * @param string $referenceNumber
     * @return Response
     */
    public function Approve(Request $request, Response $response, $args)
    {
        $referenceNumber = $args['referenceNumber'];

        Log::Debug('ReservationWriteWebService.Approve()', ['referenceNumber' => $referenceNumber, 'userId' => $this->server->GetSession()->UserId]);

        $result = $this->controller->Approve($this->server->GetSession(), $referenceNumber);

        if ($result->WasSuccessful()) {
            Log::Debug('ReservationWriteWebService.Approve() - Reservation Approved.', ['referenceNumber' => $referenceNumber]);

            return $this->server->WriteResponse(new ReservationApprovedResponse($this->server, $referenceNumber), $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('ReservationWriteWebService.Approve() - Reservation Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name CheckinReservation
     * @description Checks in to a reservation.
     * @response ReservationCheckedInResponse
     * @param string $referenceNumber
     * @return Response
     */
    public function Checkin(Request $request, Response $response, $args)
    {
        $referenceNumber = $args['referenceNumber'];
        Log::Debug('ReservationWriteWebService.Checkin()', ['referenceNumber' => $referenceNumber, 'userId' => $this->server->GetSession()->UserId]);

        $result = $this->controller->Checkin($this->server->GetSession(), $referenceNumber);

        if ($result->WasSuccessful()) {
            Log::Debug('ReservationWriteWebService.Checkin() - Reservation checked in', ['referenceNumber' => $referenceNumber]);

            return $this->server->WriteResponse(new ReservationCheckedInResponse($this->server, $referenceNumber), $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('ReservationWriteWebService.Checkin() - Reservation Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name CheckoutReservation
     * @description Checks out of a reservation.
     * @response ReservationCheckedOutResponse
     * @param string $referenceNumber
     * @return Response
     */
    public function Checkout(Request $request, Response $response, $args)
    {
        $referenceNumber = $args['referenceNumber'];
        Log::Debug('ReservationWriteWebService.Checkout()', ['referenceNumber' => $referenceNumber, 'userId' => $this->server->GetSession()->UserId]);

        $result = $this->controller->Checkout($this->server->GetSession(), $referenceNumber);

        if ($result->WasSuccessful()) {
            Log::Debug('ReservationWriteWebService.Checkout() - Reservation checked out.', ['referenceNumber' => $referenceNumber]);

            return $this->server->WriteResponse(new ReservationCheckedOutResponse($this->server, $referenceNumber), $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('ReservationWriteWebService.Checkout() - Reservation Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response, RestResponse::BAD_REQUEST_CODE);
        }
    }

    /**
     * @name DeleteReservation
     * @description Deletes an existing reservation.
     * Pass an optional updateScope query string parameter to restrict changes. Possible values for updateScope are this|full|future
     * @response DeletedResponse
     * @param string $referenceNumber
     * @return Response
     */
    public function Delete(Request $request, Response $response, $args)
    {
        $referenceNumber = $args['referenceNumber'];
        Log::Debug('ReservationWriteWebService.Delete()', ['userId' => $this->server->GetSession()->UserId, 'referenceNumber' => $referenceNumber]);

        $updateScope = $this->server->GetQueryString($request, WebServiceQueryStringKeys::UPDATE_SCOPE);
        if (empty($updateScope)) {
            $updateScope = SeriesUpdateScope::ThisInstance;
        }
        $result = $this->controller->Delete($this->server->GetSession(), $referenceNumber, $updateScope);

        if ($result->WasSuccessful()) {
            Log::Debug('ReservationWriteWebService.Delete() - Reservation Deleted.', ['referenceNumber' => $result->CreatedReferenceNumber()]);

            return $this->server->WriteResponse(new DeletedResponse(), $response, RestResponse::OK_CODE);
        } else {
            Log::Debug('ReservationWriteWebService.Delete() - Reservation Failed.');

            return $this->server->WriteResponse(new FailedResponse($this->server, $result->Errors()), $response,
                RestResponse::BAD_REQUEST_CODE);
        }
    }
}