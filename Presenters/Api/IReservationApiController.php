<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

interface IReservationApiController {
    /**
     * @return ApiActionResult
     */
    public function LoadReservation(): ApiActionResult;

    /**
     * @param ReservationApiDto|object $json
     * @return ApiActionResult
     */
    public function CalculateCredits($json): ApiActionResult;

    /**
     * @param ReservationApiDto|object $json
     * @return ApiActionResult
     */
    public function GetAttributes($json): ApiActionResult;

    /**
     * @param IReservationHandler|null $handler
     * @return void
     */
    public function CreateReservationWithFiles($handler = null): void;

    /**
     * @param IReservationHandler|null $handler
     * @return void
     */
    public function UpdateReservationWithFiles($handler = null): void;

    /**
     * @param ReservationSaveRequestApiDto|object $json
     * @param IReservationHandler|null $handler
     * @return ApiActionResult
     */
    public function CreateReservation($json, $handler = null): ApiActionResult;

    /**
     * @param ReservationSaveRequestApiDto|object $json
     * @param IReservationHandler|null $handler
     * @return ApiActionResult
     */
    public function UpdateReservation($json, $handler = null): ApiActionResult;


    /**
     * @param ReservationDeleteRequestApiDto|object $json
     * @param IReservationHandler|null $handler
     * @return ApiActionResult
     */
    public function DeleteReservation($json, $handler = null): ApiActionResult;

    /**
     * @return void
     */
    public function SaveFiles(): void;

    /**
     * @param ReservationApiDto|object $json
     * @param IReservationWaitlistRepository|null $repository
     * @return ApiActionResult
     */
    public function JoinWaitlist($json, ?IReservationWaitlistRepository $repository = null): ApiActionResult;

    /**
     * @param ReservationReferenceNumberApiDto|object $json
     * @param IReservationHandler|null $handler
     * @return ApiActionResult
     */
    public function CheckIn($json, $handler = null): ApiActionResult;

    /**
     * @param ReservationReferenceNumberApiDto|object $json
     * @param IReservationHandler|null $handler
     * @return ApiActionResult
     */
    public function CheckOut($json, $handler = null): ApiActionResult;

    /**
     * @param ReservationReferenceNumberApiDto|object $json
     * @param IReservationHandler|null $handler
     * @return ApiActionResult
     */
    public function Approve($json, $handler = null): ApiActionResult;

    /**
     * @param ReservationParticipationRequestApiDto|object $json
     * @param IParticipationHandler|null $participationHandler
     * @return ApiActionResult
     */
    public function AcceptInvite($json, $participationHandler = null): ApiActionResult;

    /**
     * @param ReservationParticipationRequestApiDto|object $json
     * @param IParticipationHandler|null $participationHandler
     * @return ApiActionResult
     */
    public function DeclineInvite($json, $participationHandler = null): ApiActionResult;

    /**
     * @param ReservationParticipationRequestApiDto|object $json
     * @param IParticipationHandler|null $participationHandler
     * @return ApiActionResult
     */
    public function CancelParticipation($json, $participationHandler = null): ApiActionResult;

    /**
     * @param ReservationParticipationRequestApiDto|object $json
     * @param IParticipationHandler|null $participationHandler
     * @return ApiActionResult
     */
    public function JoinReservation($json, $participationHandler = null): ApiActionResult;

    /**
     * @param ReservationParticipationRequestApiDto|object $json
     * @param IParticipationHandler|null $participationHandler
     * @return ApiActionResult
     */
    public function JoinReservationGuest($json, $participationHandler = null): ApiActionResult;

    /**
     * @param SendReservationAsEmailDto|object $json
     * @return ApiActionResult
     */
    public function SendAsEmail($json): ApiActionResult;

    public function SetUser(UserSession $session);
}
