<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/Reservation/ReservationAttributesPresenter.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');
require_once(ROOT_DIR . 'Presenters/Api/Reservation/ReservationAttributeLoader.php');
require_once(ROOT_DIR . 'Presenters/Api/Reservation/ReservationDtoAsAttributesPage.php');
require_once(ROOT_DIR . 'Presenters/Api/Reservation/ReservationLoader.php');
require_once(ROOT_DIR . 'Presenters/Api/Reservation/ReservationSchedulerLoader.php');
require_once(ROOT_DIR . 'Presenters/Api/Reservation/ResourceLoader.php');
require_once(ROOT_DIR . 'Presenters/Api/Reservation/ReservationApiSaveResultCollector.php');
require_once(ROOT_DIR . 'lib/Config/namespace.php');
require_once(ROOT_DIR . 'lib/Server/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Schedule/ResourceService.php');
require_once(ROOT_DIR . 'lib/Application/Authorization/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reservation/ParticipationHandler.php');
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReservationShareEmail.php');
require_once(ROOT_DIR . 'Presenters/Api/IReservationApiController.php');
require_once(ROOT_DIR . 'Pages/Api/ReservationApiPage.php');
require_once ROOT_DIR . 'Presenters/ApiDtos/ApiHelperFunctions.php';

class ReservationApiPresenter extends ActionPresenter implements IReservationApiController
{
    /**
     * @var IResourceService
     */
    private $resourceService;
    /**
     * @var IReservationApiPage
     */
    private $page;
    /**
     * @var UserSession
     */
    private $user;
    /**
     * @var IReservationViewRepository
     */
    private $reservationViewRepository;
    /**
     * @var IReservationAuthorization
     */
    private $authorization;
    /**
     * @var IAttributeService
     */
    private $attributeService;
    /**
     * @var IUserRepository
     */
    private $userRepository;
    /**
     * @var IReservationRepository
     */
    private $reservationRepository;
    /**
     * @var IPaymentRepository
     */
    private $paymentRepository;
    /**
     * @var IScheduleRepository
     */
    private $scheduleRepository;
    /**
     * @var IGroupViewRepository
     */
    private $groupRepository;

    public function __construct(IReservationApiPage        $page,
                                UserSession                $user,
                                IResourceService           $resourceService,
                                IReservationViewRepository $reservationViewRepository,
                                IReservationAuthorization  $authorization,
                                IAttributeService          $attributeService,
                                IUserRepository            $userRepository,
                                IReservationRepository     $reservationRepository,
                                IPaymentRepository         $paymentRepository,
                                IScheduleRepository        $scheduleRepository,
                                IGroupViewRepository       $groupRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->resourceService = $resourceService;
        $this->reservationViewRepository = $reservationViewRepository;
        $this->user = $user;
        $this->authorization = $authorization;
        $this->attributeService = $attributeService;
        $this->userRepository = $userRepository;
        $this->reservationRepository = $reservationRepository;
        $this->paymentRepository = $paymentRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->groupRepository = $groupRepository;

        $this->AddAction('saveFiles', 'SaveFiles');
        $this->AddAction('create', 'CreateReservationWithFiles');
        $this->AddAction('update', 'UpdateReservationWithFiles');
        $this->AddApi('load', 'LoadReservation');
        $this->AddApi('credits', 'CalculateCredits');
        $this->AddApi('attributes', 'GetAttributes');
        $this->AddApi('delete', 'DeleteReservation');
        $this->AddApi('waitlist', 'JoinWaitlist');
        $this->AddApi('waitlistRemove', 'RemoveFromWaitlist');
        $this->AddApi('checkin', 'CheckIn');
        $this->AddApi('checkout', 'CheckOut');
        $this->AddApi('approve', 'Approve');
        $this->AddApi('acceptInvite', 'AcceptInvite');
        $this->AddApi('declineInvite', 'DeclineInvite');
        $this->AddApi('cancelParticipation', 'CancelParticipation');
        $this->AddApi('join', 'JoinReservation');
        $this->AddApi('joinAsGuest', 'JoinReservationGuest');
        $this->AddApi('sendAsEmail', 'SendAsEmail');
    }

    /**
     * @return ReservationApiPresenter
     */
    public static function Create(): ReservationApiPresenter
    {
        $permissionService = PluginManager::Instance()->LoadPermission();

        return new ReservationApiPresenter(
            new ReservationApiPage(),
            ServiceLocator::GetServer()->GetUserSession(),
            ResourceService::Create(),
            new ReservationViewRepository(),
            new ReservationAuthorization(new AuthorizationService(new UserRepository())),
            new AttributeService(new AttributeRepository(), $permissionService),
            new UserRepository(),
            new ReservationRepository(),
            new PaymentRepository(),
            new ScheduleRepository(),
            new GroupRepository());
    }

    public function LoadReservation(): ApiActionResult
    {
        $reservationLoader = new ReservationLoader($this->page, $this->reservationViewRepository);
        try {
            $reservation = $reservationLoader->LoadFromReferenceNumber();
        } catch (Exception $exception) {
            Log::Error("Could not load reservation.", ['exception' => $exception]);
            return new ApiActionResult(false, null, new ApiErrorList(["Reservation Not Found"]));
        }

        $scheduleLoader = new ReservationSchedulerLoader($this->page, $this->scheduleRepository, $this->resourceService, $reservation);
        $privacyFilter = new PrivacyFilter($this->authorization);

        $schedule = $scheduleLoader->Load($this->user->Timezone);

        if (empty($schedule)) {
            Log::Error("Could not load reservation because no schedule could be found");
            return new ApiActionResult(false, null, new ApiErrorList(["Invalid Request"]));
        }

        $resourceLoader = new ResourceLoader($this->resourceService, $this->attributeService);
        $resourceAttributes = $resourceLoader->LoadAttributes($this->user);
        $resources = $resourceLoader->Load($schedule->schedule->GetId(), $this->user);

        if (empty($reservation)) {
            $reservation = $reservationLoader->Load($schedule->schedule, $resources, $this->page, $this->user, $this->scheduleRepository);
        }

        try {
            if (empty($resources)) {
                foreach ($reservation->ResourceIds() as $resourceId) {
                    $resources[] = $this->resourceService->GetResource($resourceId);
                }
            }

            $response = new ReservationResponseApiDto();

            $response->schedule = ReservationScheduleApiDto::FromSchedule($schedule->schedule, $schedule->layout);
            if ($schedule->schedule->HasCustomLayout()) {
                $response->schedule->upcomingAppointments = UpcomingAppointmentDto::FromPeriods($this->scheduleRepository->GetCustomLayoutPeriodsInRange($reservation->StartDate, $reservation->EndDate->AddDays(7), $schedule->schedule->GetId()));
            }
            $response->resources = ResourceApiDto::FromList($resources, $resourceAttributes);
            $response->resourceGroups = ResourceGroupApiDto::FromList($this->resourceService->GetResourceGroupList());
            $response->resourceAttributes = AttributeApiDto::FromList($resourceAttributes);
            $response->terms = ReservationTermsApiDto::FromTerms((new TermsOfServiceRepository())->Load());
            $authView = ReservationAuthorizationView::ConvertView($reservation);
            $canApprove = $this->authorization->CanApprove($authView, $this->user);
            $isAdmin = $this->authorization->IsAdmin($authView, $this->user);
//            $isAccessibleTime = $isAdmin || $this->authorization->IsTimeAccessible($authView, $this->user);
            $canEdit = $isAdmin || empty($reservation->ReferenceNumber) || $this->authorization->CanEdit($authView, $this->user);
            $canChangeUser = $this->authorization->CanChangeUsers($this->user);
            $canView = $isAdmin || $privacyFilter->CanViewDetails($this->user, $reservation, $reservation->OwnerId);
            $response->reservation = ReservationApiDto::FromView($reservation, $this->user->Timezone);
            $response->accessories = AccessoryApiDto::FromList($this->resourceService->GetAccessories());
            $response->users = [];
            $response->canApprove = $canApprove;
            $response->canEdit = $canEdit;
            $response->canChangeUser = $canChangeUser;
            $response->canView = $canView;
            $response->checkin = ReservationCheckinApiDto::FromView($reservation, $isAdmin, $canEdit);

            $waitlistRequests = [];

            if (!empty($reservation->ReferenceNumber) && Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_WAITLIST, new BooleanConverter())) {
                $resourceIds = $reservation->ResourceIds();
                $repo = new ReservationWaitlistRepository();
                $waitlistRequests = $repo->FindWaitlistRequests($resourceIds, $reservation->StartDate, $reservation->EndDate);
            }

            $response->isWaitlisted = $this->IsUserOnWaitlist($waitlistRequests);
            $response->waitlistCount = count($waitlistRequests);

            $canViewUser = $isAdmin || $privacyFilter->CanViewUser($this->user, $authView, $reservation->OwnerId);
            $showName = Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_ALWAYS_SHOW_USER_NAME, new BooleanConverter());
            $hideEmail = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_HIDE_EMAIL, new BooleanConverter());

            if ($canEdit || ($canViewUser && $canView)) {
                $groupList = $this->groupRepository->GetList()->Results();
                $response->groups = GroupsApiDto::FromList($groupList);
                $response->users = UserApiDto::FromList($this->userRepository->GetAll(), $groupList, !$canViewUser, $showName, $hideEmail, $this->user->UserId);
            }
            $response->Censor($canViewUser, $canView);

            $response->meetingConnections = new ReservationMeetingConnectionsApiDto();
            if (Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_ALLOW_MEETING_LINKS, new BooleanConverter())) {
                $oauth = $this->userRepository->GetAllOAuth($this->user->UserId);
                $response->meetingConnections = ReservationMeetingConnectionsApiDto::FromList($oauth);
            }

            return new ApiActionResult(true, $response);
        } catch (Exception $ex) {
            Log::Error("Error during LoadReservation.", ['exception' => $ex]);
            return new ApiActionResult(false, null, new ApiErrorList(["Error loading reservation"]));
        }
    }

    public function CalculateCredits($json): ApiActionResult
    {
        /** @var ReservationApiDto $request */
        $request = $json;

        if (count($request->resourceIds) === 0) {
            return new ApiActionResult(true, ['available' => 0, 'required' => 0, 'totalCost' => null]);
        }

        if (!Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter())) {
            return new ApiActionResult(true, ['available' => 0, 'required' => 0, 'totalCost' => null]);
        }

        $owner = $this->userRepository->LoadById($request->ownerId);
        if ($request->ownerId != $this->user->UserId) {
            $currentUser = $this->userRepository->LoadById($this->user->UserId);

            if (!$currentUser->IsAdminFor($owner)) {
                return new ApiActionResult(true, ['available' => 0, 'required' => 0, 'totalCost' => null]);
            }
        }

        $reservation = $this->GetReservation($request);
        $layout = $this->scheduleRepository->GetLayout($reservation->ScheduleId(), new ScheduleLayoutFactory($this->user->Timezone));
        $reservation->CalculateCredits($layout);
        $creditsRequired = $reservation->GetCreditsRequired();

        $cost = null;
        if (Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ALLOW_PURCHASE, new BooleanConverter())) {
            $creditCost = $this->paymentRepository->GetCreditCost();
            $cost = $creditCost->GetFormattedTotal($creditsRequired);
        }

        return new ApiActionResult(true, ['available' => floatval($owner->GetCurrentCredits()), 'required' => floatval($creditsRequired), 'totalCost' => empty($cost) ? null : $cost]);
    }

    /**
     * @param ReservationApiDto $request
     * @return ExistingReservationSeries|ReservationSeries
     */
    private function GetReservation($request)
    {
        $referenceNumber = $request->referenceNumber;

        $duration = (new DateRange(Date::ParseExact($request->start), Date::ParseExact($request->end)))->ToTimezone(ServiceLocator::GetServer()->GetUserSession()->Timezone);
        $roFactory = new RepeatOptionsFactory();
        $recurrence = $request->recurrence;
        $repeatDates = [];
        if ($recurrence->repeatDates !== null) {
            $repeatDates = array_map(function ($d) {
                return Date::Parse($d, $this->user->Timezone);
            }, $recurrence->repeatDates);
        }
        $repeatOptions = $roFactory->Create($recurrence->type,
            $recurrence->interval,
            Date::Parse($recurrence->terminationDate, $this->user->Timezone),
            $recurrence->weekdays,
            $recurrence->monthlyType,
            $repeatDates
        );

        $resourceIds = $request->resourceIds;
        $primaryResourceId = array_shift($resourceIds);

        if (empty($referenceNumber)) {
            $userId = $request->ownerId;

            $resource = $this->resourceService->GetResource($primaryResourceId);

            $reservationSeries = ReservationSeries::Create($userId, $resource, null, null, $duration, $repeatOptions, $this->user);
            foreach ($resourceIds as $resourceId) {
                if ($primaryResourceId != $resourceId) {
                    $reservationSeries->AddResource($this->resourceService->GetResource($resourceId));
                }
            }

            $reservedAccessories = $request->accessories;
            foreach ($reservedAccessories as $reservedAccessory) {
                $accessory = $this->resourceService->GetAccessoryById($reservedAccessory->id);
                $reservationSeries->AddAccessory(new ReservationAccessory($accessory, $reservedAccessory->quantityReserved));
            }

            return $reservationSeries;
        }

        $existingSeries = $this->reservationRepository->LoadByReferenceNumber($referenceNumber);

        $resource = $this->resourceService->GetResource($primaryResourceId);
        $existingSeries->Update(
            $request->ownerId,
            $resource,
            null,
            null,
            $this->user);

        $existingSeries->UpdateDuration($duration);

        $existingSeries->Repeats($repeatOptions);

        $additionalResources = array();
        foreach ($resourceIds as $additionalResourceId) {
            if ($additionalResourceId != $primaryResourceId) {
                $additionalResources[] = $this->resourceService->GetResource($additionalResourceId);
            }
        }

        $existingSeries->ChangeResources($additionalResources);

        $accessories = array();
        $reservedAccessories = $request->accessories;
        foreach ($reservedAccessories as $reservedAccessory) {
            $accessory = $this->resourceService->GetAccessoryById($reservedAccessory->id);
            $accessories[] = new ReservationAccessory($accessory, $reservedAccessory->quantityReserved);
        }

        $existingSeries->ChangeAccessories($accessories);

        return $existingSeries;
    }

    public function GetAttributes($json): ApiActionResult
    {
        /** @var ReservationApiDto $request */
        $request = $json;

        $pageWrapper = new ReservationDtoAsAttributesPage($request);
        $reservationAttributesPresenter = new ReservationAttributesPresenter(
            $pageWrapper,
            $this->attributeService,
            new PrivacyFilter($this->authorization), $this->reservationViewRepository);

        $reservationAttributesPresenter->PageLoad(ServiceLocator::GetServer()->GetUserSession());

        return new ApiActionResult(true, $pageWrapper->GetData());
    }

    /**
     * @param ReservationSeries|ExistingReservationSeries| null $series
     * @param ReservationHandler $handler
     * @param ReservationSaveRequestApiDto $request
     * @return ApiActionResult
     */
    private function SaveReservation($series, $handler, $request): ApiActionResult
    {
        if (empty($series)) {
            return new ApiActionResult(false, null, new ApiErrorList(["Reservation not found"]));
        }

        try {
            $resultCollector = new ReservationApiSaveResultCollector($request->retryParameters);
            $ok = $handler->Handle($series, $resultCollector);

            if (!$ok) {
                Log::Debug("Validation errors prevented saving reservation");
                $referenceNumber = $request->reservation->referenceNumber;
            } else {
                $referenceNumber = $series->CurrentInstance()->ReferenceNumber();
                Log::Debug("Reservation saved.", ['referenceNumber' => $referenceNumber]);
            }

            $response = new ReservationSaveResponseApiDto();
            $response->referenceNumber = $referenceNumber;
            $response->requiresApproval = $series->RequiresApproval();
            $response->success = $resultCollector->GetWasSuccessful();
            $response->canBeRetried = $resultCollector->GetCanBeRetried();
            $response->canJoinWaitlist = $resultCollector->GetCanJoinWaitlist();
            $response->errors = $resultCollector->GetErrors();
            $response->warnings = $resultCollector->GetWarnings();
            $response->retryMessages = $resultCollector->GetRetryMessages();
            $response->retryParameters = [];
            if (!empty($resultCollector->GetRetryParameters())) {
                $response->retryParameters = array_map(function ($i) {
                    return ReservationRetryParameterApiDto::Create($i->Name(), $i->Value());
                }, $resultCollector->GetRetryParameters());
            }
            $response->showDetails = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_SHOW_DETAILED_SAVE_RESPONSE, new BooleanConverter());
            $response->dates = [];
            if ($response->showDetails) {
                foreach ($series->Instances() as $i) {
                    $response->dates[] = $i->StartDate()->ToTimezone($this->user->Timezone)->Format(Resources::GetInstance()->GeneralDateFormat());
                }
            }

            Log::Debug("Saved reservation.", ['referenceNumber' => $response->referenceNumber]);

            return new ApiActionResult(true, $response);
        } catch (Exception $ex) {
            Log::Error("Error saving reservation.", ['exception' => $ex]);
            return new ApiActionResult(false, null, new ApiErrorList(["Unknown error"]));
        }
    }

    public function CreateReservationWithFiles($handler = null): void
    {
        $this->page->SetJsonResponse($this->CreateReservation($this->page->GetRequest(), $handler));
    }

    public function CreateReservation($json, $handler = null): ApiActionResult
    {
        /** @var ReservationSaveRequestApiDto $request */
        $request = $json;

        if (empty($handler)) {
            $handler = ReservationHandler::Create(ReservationAction::Create, null, $this->user);
        }

        $series = $this->BuildReservation($request->reservation);

        return $this->SaveReservation($series, $handler, $request);
    }

    public function UpdateReservationWithFiles($handler = null): void
    {
        $this->page->SetJsonResponse($this->UpdateReservation($this->page->GetRequest(), $handler));
    }

    public function UpdateReservation($json, $handler = null): ApiActionResult
    {
        /** @var ReservationSaveRequestApiDto $request */
        $request = $json;

        if (empty($handler)) {
            $handler = ReservationHandler::Create(ReservationAction::Update, null, $this->user);
        }

        $series = $this->BuildUpdateReservation($request->reservation, $request->updateScope);

        return $this->SaveReservation($series, $handler, $request);
    }

    public function DeleteReservation($json, $handler = null): ApiActionResult
    {
        /** @var ReservationDeleteRequestApiDto $request */
        $request = $json;
        if (empty($handler)) {
            $handler = ReservationHandler::Create(ReservationAction::Delete, null, $this->user);
        }

        $series = $this->reservationRepository->LoadByReferenceNumber($request->referenceNumber);
        if (is_null($series)) {
            return new ApiActionResult(false, null, new ApiErrorList(["Reservation not found"]));
        }

        $series->ApplyChangesTo($request->scope, $this->authorization->IsAdmin(ReservationAuthorizationView::ConvertSeries($series), $this->user));
        $series->Delete($this->user, $request->reason);

        if (!$this->authorization->CanEdit(ReservationAuthorizationView::ConvertSeries($series), $this->user)) {
            Log::Error("User does not have permission to delete this reservation");
            return new ApiActionResult(false, null, new ApiErrorList(["You do not have permission to delete this reservation"]));
        }

        try {
            $resultCollector = new ReservationApiSaveResultCollector([]);
            $ok = $handler->Handle($series, $resultCollector);

            Log::Debug("Deleted reservation", ['referenceNumber' => $request->referenceNumber]);

            if (!$ok) {
                Log::Error("Validation errors prevented deleting reservation");
            } else {
                $referenceNumber = $series->CurrentInstance()->ReferenceNumber();
                Log::Debug("Reservation deleted.", ['referenceNumber' => $referenceNumber]);
            }

            $response = new ReservationGenericResponseApiDto();
            $response->success = $resultCollector->GetWasSuccessful();
            $response->errors = $resultCollector->GetErrors();
            return new ApiActionResult(true, $response);
        } catch (Exception $ex) {
            Log::Error("Error saving reservation", ['exception' => $ex]);
            return new ApiActionResult(false, null, new ApiErrorList(["Unknown error"]));
        }
    }

    public function SaveFiles(): void
    {
        try {
            $referenceNumber = $this->page->GetApiReferenceNumber();

            Log::Debug("Saving reservation files.", ['referenceNumber' => $referenceNumber]);

            $series = $this->reservationRepository->LoadByReferenceNumber($referenceNumber);
            if (!$this->authorization->CanEdit(ReservationAuthorizationView::ConvertSeries($series), $this->user)) {
                Log::Error("User does not have permission to add attachments to this reservation");
                $this->page->SetJsonResponse(null, ["You do not have permission to save this reservation"], 400);
            }
            $files = $this->page->GetUploads();

            foreach ($files as $file) {
                if ($file->IsError()) {
                    Log::Error('Error attaching file', ['fileName' => $file->OriginalName(), 'error' => $file->Error()]);
                } else {
                    $attachment = ReservationAttachment::Create($file->OriginalName(), $file->MimeType(), $file->Size(), $file->Contents(), $file->Extension(), $series->SeriesId());
                    $id = $this->reservationRepository->AddReservationAttachment($attachment);
                    $attachment->WithFileId($id);
                    $series->AddAttachment($attachment);
                }
            }

            Log::Debug("Save reservation files.", ['count', count($files)]);

            $this->page->SetJsonResponse(['data' => ['success' => true]]);
        } catch (Exception $ex) {
            Log::Error("Error saving reservation attachments", ['exception' => $ex]);
            $this->page->SetJsonResponse(null, ["Unknown error"], 400);
        }
    }

    /**
     * @param DateRange $duration
     * @param string[]|null $repeatDates
     * return Date[]
     */
    private function ParseRepeatDates(DateRange $duration, $repeatDates)
    {
        if (empty($repeatDates)) {
            return [];
        }
        $parsed = [];
        foreach ($repeatDates as $d) {
            $parsed[] = Date::ParseExact($d)->ToTimezone($duration->Timezone())->SetTime($duration->GetBegin()->GetTime());
        }

        return $parsed;
    }

    /**
     * @param ReservationApiDto $request
     * @return ReservationSeries
     */
    public function BuildReservation($request): ReservationSeries
    {
        $userId = intval($request->ownerId);
        $primaryResourceId = intval($request->resourceIds[0]);
        $resource = $this->resourceService->GetResource($primaryResourceId);
        $title = apiencode($request->title . '');
        $description = apiencode($request->description . '');
        $duration = (new DateRange(Date::ParseExact($request->start), Date::ParseExact($request->end)))->ToTimezone(ServiceLocator::GetServer()->GetUserSession()->Timezone);
        $roFactory = new RepeatOptionsFactory();
        $recurrence = $request->recurrence;
        $repeatOptions = $roFactory->Create($recurrence->type, $recurrence->interval, Date::ParseExact($recurrence->terminationDate), $recurrence->weekdays, $recurrence->monthlyType, $this->ParseRepeatDates($duration, $recurrence->repeatDates));
        $coowners = array_map('intval', $request->coOwnerIds);

        $reservationSeries = ReservationSeries::Create($userId, $resource, $title, $description, $duration, $repeatOptions, $this->user, $coowners);

        foreach ($request->resourceIds as $resourceId) {
            if ($primaryResourceId != $resourceId) {
                $reservationSeries->AddResource($this->resourceService->GetResource($resourceId));
            }
        }

        foreach ($request->accessories as $reservedAccessory) {
            $accessory = $this->resourceService->GetAccessoryById($reservedAccessory->id);
            $reservationSeries->AddAccessory(new ReservationAccessory($accessory, $reservedAccessory->quantityReserved));
        }

        foreach ($request->attributeValues as $attribute) {
            $reservationSeries->AddAttributeValue(new AttributeValue($attribute->id, apiencode($attribute->value . '')));
        }

        $reservationSeries->ChangeInvitees($request->inviteeIds);
        $reservationSeries->ChangeGuests(apiencode( $request->guestEmails), []);
        $reservationSeries->AllowParticipation($request->allowSelfJoin);

        if (!empty($request->startReminder)) {
            $reservationSeries->AddStartReminder(new ReservationReminder($request->startReminder->value, $request->startReminder->interval));
        }

        if (!empty($request->endReminder)) {
            $reservationSeries->AddEndReminder(new ReservationReminder($request->endReminder->value, $request->endReminder->interval));
        }

        if (Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter())) {
            $layout = $this->scheduleRepository->GetLayout($reservationSeries->ScheduleId(), new ScheduleLayoutFactory($this->user->Timezone));
            $reservationSeries->CalculateCredits($layout);
        }

        $reservationSeries->AcceptTerms(!empty($request->termsAcceptedDate));

        $files = $this->page->GetUploads();
        foreach ($files as $file) {
            if ($file->IsError()) {
                Log::Error('Error attaching file', ['fileName' => $file->OriginalName(), 'error' => $file->Error()]);
            } else {
                $attachment = ReservationAttachment::Create($file->OriginalName(), $file->MimeType(), $file->Size(), $file->Contents(), $file->Extension(), 0);
                $reservationSeries->AddAttachment($attachment);
            }
        }

        if (!empty($request->meetingLink)) {
            $reservationSeries->AddMeetingLink($request->meetingLink->type, apiencode($request->meetingLink->url));
        }

        return $reservationSeries;
    }

    /**
     * @param ReservationApiDto $request
     * @param SeriesUpdateScope|string $updateScope
     * @return ReservationSeries|null
     */
    public function BuildUpdateReservation($request, $updateScope): ?ReservationSeries
    {
        $existingSeries = $this->reservationRepository->LoadByReferenceNumber($request->referenceNumber);

        if (empty($existingSeries)) {
            return null;
        }

        $primaryResourceId = intval($request->resourceIds[0]);
        $recurrence = $request->recurrence;
        $roFactory = new RepeatOptionsFactory();
        $duration = (new DateRange(Date::ParseExact($request->start), Date::ParseExact($request->end)))->ToTimezone(ServiceLocator::GetServer()->GetUserSession()->Timezone);

        $repeatOptions = $roFactory->Create($recurrence->type, $recurrence->interval, Date::ParseExact($recurrence->terminationDate), $recurrence->weekdays, $recurrence->monthlyType, $this->ParseRepeatDates($duration, $recurrence->repeatDates));

        $existingSeries->ApplyChangesTo($updateScope, $this->authorization->IsAdmin(ReservationAuthorizationView::ConvertSeries($existingSeries), $this->user));

        $resource = $this->resourceService->GetResource($primaryResourceId);
        $existingSeries->Update(
            $request->ownerId,
            $resource,
            apiencode($request->title),
            apiencode($request->description),
            $this->user);

        $existingSeries->UpdateDuration($duration);
        $existingSeries->Repeats($repeatOptions);

        $additionalResources = array();
        foreach ($request->resourceIds as $additionalResourceId) {
            if ($additionalResourceId != $primaryResourceId) {
                $additionalResources[] = $this->resourceService->GetResource($additionalResourceId);
            }
        }

        $existingSeries->ChangeResources($additionalResources);

        $existingSeries->ChangeCoOwners($request->coOwnerIds);
        $existingSeries->ChangeParticipants($request->participantIds);
        $existingSeries->ChangeInvitees($request->inviteeIds);
        $existingSeries->ChangeGuests(apiencode( $request->guestEmails), apiencode($request->participantEmails));
        $existingSeries->AllowParticipation($request->allowSelfJoin);

        $accessories = [];
        foreach ($request->accessories as $reservedAccessory) {
            $accessory = $this->resourceService->GetAccessoryById($reservedAccessory->id);
            $accessories[] = new ReservationAccessory($accessory, $reservedAccessory->quantityReserved);
        }
        $existingSeries->ChangeAccessories($accessories);

        $attributeValues = [];
        foreach ($request->attributeValues as $attribute) {
            $attributeValues[] = new AttributeValue($attribute->id, apiencode($attribute->value));
        }
        $existingSeries->ChangeAttributes($attributeValues);

        if (!empty($request->startReminder)) {
            $existingSeries->AddStartReminder(new ReservationReminder($request->startReminder->value, $request->startReminder->interval));
        } else {
            $existingSeries->RemoveStartReminder();
        }

        if (!empty($request->endReminder)) {
            $existingSeries->AddEndReminder(new ReservationReminder($request->endReminder->value, $request->endReminder->interval));
        } else {
            $existingSeries->RemoveEndReminder();
        }

        if (Configuration::Instance()->GetSectionKey(ConfigSection::CREDITS, ConfigKeys::CREDITS_ENABLED, new BooleanConverter())) {
            $layout = $this->scheduleRepository->GetLayout($existingSeries->ScheduleId(), new ScheduleLayoutFactory($this->user->Timezone));
            $existingSeries->CalculateCredits($layout);
        }

        $files = $this->page->GetUploads();
        foreach ($files as $file) {
            if ($file->IsError()) {
                Log::Error('Error attaching file', ['fileName' => $file->OriginalName(), 'error' => $file->Error()]);
            } else {
                $attachment = ReservationAttachment::Create($file->OriginalName(), $file->MimeType(), $file->Size(), $file->Contents(), $file->Extension(), $existingSeries->SeriesId());
                $existingSeries->AddAttachment($attachment);
            }
        }

        if (empty($request->meetingLink)) {
            $existingSeries->RemoveMeetingLink();
        } else {
            $existingSeries->UpdateMeetingLink($request->meetingLink->type, apiencode($request->meetingLink->url));
        }

        return $existingSeries;
    }

    public function JoinWaitlist($json, ?IReservationWaitlistRepository $repository = null): ApiActionResult
    {
        try {
            /** @var ReservationApiDto $request */
            $request = $json;

            if (!$repository) {
                $repository = new ReservationWaitlistRepository();
            }

            $resourceId = intval($request->resourceIds[0]);
            Log::Debug("Adding reservation waitlist request.", ['userId' => $this->user->UserId, 'start' => $request->start, 'end' => $request->end, 'resourceId' => $resourceId]);
            $repository->Add(new ReservationWaitlistRequest(0, $this->user->UserId, Date::ParseExact($request->start), Date::ParseExact($request->end), $resourceId));

            $response = new ReservationGenericResponseApiDto();
            $response->success = true;
            $response->errors = [];
            return new ApiActionResult(true, $response);

        } catch (Exception $ex) {
            Log::Error("Error saving waitlist", ['exception' => $ex]);
            return new ApiActionResult(false, null, new ApiErrorList(["Unknown error"]));
        }
    }

    public function RemoveFromWaitlist($json, ?IReservationWaitlistRepository $repository = null): ApiActionResult
    {
        try {
            /** @var ReservationApiDto $request */
            $request = $json;

            if (!$repository) {
                $repository = new ReservationWaitlistRepository();
            }

            $referenceNumber = $request->referenceNumber;
            Log::Debug("Removing user from reservation waitlist.", ['userId' => $this->user->UserId, 'referenceNumber' => $referenceNumber]);
            $reservation = $this->reservationViewRepository->GetReservationForEditing($referenceNumber);
            $request = $repository->FindWaitlistRequest($this->user->UserId, $reservation->ResourceIds(), $reservation->StartDate, $reservation->EndDate);
            $repository->Delete($request);

            $response = new ReservationGenericResponseApiDto();
            $response->success = true;
            $response->errors = [];
            return new ApiActionResult(true, $response);

        } catch (Exception $ex) {
            Log::Error("Error removing from waitlist", ['exception' => $ex]);
            return new ApiActionResult(false, null, new ApiErrorList(["Unknown error"]));
        }
    }

    public function CheckIn($json, $handler = null): ApiActionResult
    {
        return $this->CheckInOrCheckOut($json, ReservationAction::Checkin, $handler);
    }

    public function CheckOut($json, $handler = null): ApiActionResult
    {
        return $this->CheckInOrCheckOut($json, ReservationAction::Checkout, $handler);
    }

    public function CheckInOrCheckOut($json, $action, $handler = null): ApiActionResult
    {
        try {
            /** @var ReservationReferenceNumberApiDto $request */
            $request = $json;

            if (empty($handler)) {
                $handler = ReservationHandler::Create($action, null, $this->user);
            }

            $series = $this->reservationRepository->LoadByReferenceNumber($request->referenceNumber);

            if (!$this->authorization->CanEdit(ReservationAuthorizationView::ConvertSeries($series), $this->user, true)) {
                Log::Error("User does not have permission to save this reservation");
                return new ApiActionResult(false, null, new ApiErrorList(["You do not have permission to save this reservation"]));
            }

            if ($action === ReservationAction::Checkin) {
                $series->Checkin($this->user);
                Log::Debug("Reservation checkin.", ['referenceNumber' => $request->referenceNumber]);
            } else {
                $series->Checkout($this->user);
                Log::Debug("Reservation checkout.", ['referenceNumber' => $request->referenceNumber]);
            }

            $resultCollector = new ReservationApiSaveResultCollector([]);
            $ok = $handler->Handle($series, $resultCollector);

            if (!$ok) {
                Log::Error("Validation errors prevented checking in reservation");
            } else {
                $referenceNumber = $series->CurrentInstance()->ReferenceNumber();
                Log::Debug("Reservation checked in.", ['referenceNumber' => $referenceNumber]);
            }

            $view = $this->reservationViewRepository->GetReservationForEditing($request->referenceNumber);
            return new ApiActionResult(true, ReservationApiDto::FromView($view, $this->user->Timezone));
        } catch (Exception $ex) {
            Log::Error("Error checking in reservation.", ['exception' => $ex]);
            return new ApiActionResult(false, null, new ApiErrorList(["Unknown error"]));
        }
    }

    public function Approve($json, $handler = null): ApiActionResult
    {
        /** @var ReservationReferenceNumberApiDto $request */
        $request = $json;
        try {
            if (empty($handler)) {
                $handler = ReservationHandler::Create(ReservationAction::Approve, null, $this->user);
            }

            $series = $this->reservationRepository->LoadByReferenceNumber($request->referenceNumber);
            if ($this->authorization->CanApprove(ReservationAuthorizationView::ConvertSeries($series), $this->user)) {
                $series->Approve($this->user);

                $resultCollector = new ReservationApiSaveResultCollector([]);
                $ok = $handler->Handle($series, $resultCollector);

                if (!$ok) {
                    Log::Error("Validation errors prevented checking in reservation");
                } else {
                    $referenceNumber = $series->CurrentInstance()->ReferenceNumber();
                    Log::Debug("Reservation approved.", ['referenceNumber' => $referenceNumber]);
                }

                $view = $this->reservationViewRepository->GetReservationForEditing($request->referenceNumber);
                return new ApiActionResult(true, ReservationApiDto::FromView($view, $this->user->Timezone));
            }

            Log::Error("Not authorized to approve.", ['referenceNumber' => $request->referenceNumber]);
            return new ApiActionResult(false, null, new ApiErrorList(["Unknown error"]));
        } catch
        (Exception $ex) {
            Log::Error("Error approving reservation.", ['exception' => $ex]);
            return new ApiActionResult(false, null, new ApiErrorList(["Unknown error"]));
        }
    }

    /**
     * @param $requests ReservationWaitlistRequest[]
     * @return bool
     */
    private function IsUserOnWaitlist($requests)
    {
        foreach ($requests as $request) {
            if ($request->UserId() == $this->user->UserId) {
                return true;
            }
        }
        return false;
    }

    public function AcceptInvite($json, $participationHandler = null): ApiActionResult
    {
        if (empty($participationHandler)) {
            $participationHandler = ParticipationHandler::Create($this->user);
        }

        /** @var ReservationParticipationRequestApiDto $request */
        $request = $json;

        $handleResult = $participationHandler->HandleAccept($request->referenceNumber, $request->fullSeries);

        return new ApiActionResult(true, ReservationGenericResponseApiDto::Create($handleResult->success, $handleResult->errors));
    }

    public function DeclineInvite($json, $participationHandler = null): ApiActionResult
    {
        if (empty($participationHandler)) {
            $participationHandler = ParticipationHandler::Create($this->user);
        }

        /** @var ReservationParticipationRequestApiDto $request */
        $request = $json;

        $handleResult = $participationHandler->HandleDecline($request->referenceNumber, $request->fullSeries);

        return new ApiActionResult(true, ReservationGenericResponseApiDto::Create($handleResult->success, $handleResult->errors));
    }

    public function CancelParticipation($json, $participationHandler = null): ApiActionResult
    {
        if (empty($participationHandler)) {
            $participationHandler = ParticipationHandler::Create($this->user);
        }

        /** @var ReservationParticipationRequestApiDto $request */
        $request = $json;

        $handleResult = $participationHandler->HandleCancel($request->referenceNumber, $request->fullSeries);

        return new ApiActionResult(true, ReservationGenericResponseApiDto::Create($handleResult->success, $handleResult->errors));
    }

    public function JoinReservation($json, $participationHandler = null): ApiActionResult
    {
        if (empty($participationHandler)) {
            $participationHandler = ParticipationHandler::Create($this->user);
        }

        /** @var ReservationParticipationRequestApiDto $request */
        $request = $json;

        $handleResult = $participationHandler->HandleJoin($request->referenceNumber, $request->fullSeries);

        return new ApiActionResult(true, ReservationGenericResponseApiDto::Create($handleResult->success, $handleResult->errors));
    }

    public function JoinReservationGuest($json, $participationHandler = null): ApiActionResult
    {
        if (empty($participationHandler)) {
            $participationHandler = ParticipationHandler::Create($this->user);
        }

        /** @var ReservationParticipationRequestApiDto $request */
        $request = $json;

        $handleResult = $participationHandler->HandleJoinGuest($request->referenceNumber, $request->emailAddress, $request->fullSeries);

        return new ApiActionResult(true, ReservationGenericResponseApiDto::Create($handleResult->success, $handleResult->errors));
    }

    public function SendAsEmail($json): ApiActionResult
    {
        /** @var SendReservationAsEmailDto $request */
        $request = $json;

        $existingSeries = $this->reservationRepository->LoadByReferenceNumber($request->referenceNumber);
        if ($this->authorization->CanViewDetails(ReservationAuthorizationView::ConvertSeries($existingSeries), $this->user)) {
            $owner = $this->userRepository->LoadById($existingSeries->UserId());
            $existingSeries->UpdateBookedBy(ServiceLocator::GetServer()->GetUserSession());

            $emails = array_map('trim', preg_split('/[\s,;]/', $request->emails, -1, PREG_SPLIT_NO_EMPTY));
            foreach ($emails as $emailAddress) {

                try {
                    Log::Debug('Emailing reservation details.',
                        ['referenceNumber' => $existingSeries->CurrentInstance()->ReferenceNumber(),
                            'userId' => $this->user->UserId, 'email' => $emailAddress]);

                    $email = new ReservationShareEmail($owner, $emailAddress, $existingSeries, new AttributeRepository(), $this->userRepository);
                    ServiceLocator::GetEmailService()->Send($email);
                } catch (Exception $ex) {
                    Log::Error("Failed sending reservation as email.", ['exception' => $ex]);
                }
            }

            return new ApiActionResult(true, ReservationGenericResponseApiDto::Create(true));
        }

        Log::Error("Could not send reservation as email - no permission.", ['referenceNumber' => $request->referenceNumber]);
        return new ApiActionResult(false, ReservationGenericResponseApiDto::Create(false, ["You do not have permission to email this reservation"]));
    }

    public function SetUser(UserSession $user)
    {
        $this->user = $user;
    }
}