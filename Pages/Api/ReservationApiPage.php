<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/ActionPage.php');
require_once(ROOT_DIR . 'Presenters/Api/ReservationApiPresenter.php');

interface IReservationApiPage extends IActionPage
{
    /**
     * @return int|null
     */
    public function GetScheduleId(): ?int;

    /**
     * @return int[]|null
     */
    public function GetResourceIds(): ?array;

    /**
     * @return string|null
     */
    public function GetResourcePublicId(): ?string;

    /**
     * @return string|null
     */
    public function GetReferenceNumber(): ?string;

    /**
     * @return string|null
     */
    public function GetSourceReferenceNumber(): ?string;

    /**
     * @return string|null
     */
    public function GetReservationDate(): ?string;

    /**
     * @return string|null
     */
    public function GetStartDate(): ?string;

    /**
     * @return string|null
     */
    public function GetEndDate(): ?string;

    /**
     * @return array|UploadedFile[]
     */
    public function GetUploads(): array;

    public function GetRequest();

    /**
     * @return string|null
     */
    public function GetApiReferenceNumber(): ?string;
}

class ReservationApiPage extends ActionPage implements IReservationApiPage
{
    /**
     * @var ReservationApiPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct("", 1);
        $permissionService = PluginManager::Instance()->LoadPermission();
        $this->presenter = new ReservationApiPresenter(
            $this,
            $this->server->GetUserSession(),
            ResourceService::Create(),
            new ReservationViewRepository(),
            new ReservationAuthorization(new AuthorizationService(new UserRepository())),
            new AttributeService(new AttributeRepository(), $permissionService),
            new UserRepository(),
            new ReservationRepository(),
            new PaymentRepository(),
            new ScheduleRepository(),
            new GroupRepository(),
        );
    }

    public function ProcessAction()
    {
        if ($this->AllowAccess()) {
            $this->presenter->ProcessAction();
        } else {
            $this->SetJsonResponse(['Unauthorized' => true], 'Unauthorized', 401);
        }
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessPageLoad()
    {
        // no-op
    }

    protected function ProcessApiCall($json)
    {
        if ($this->AllowAccess()) {
            $this->presenter->ProcessApi($json);
        } else {
            $this->SetJsonResponse(['Unauthorized' => true], 'Unauthorized', 401);
        }
    }

    /**
     * @return bool
     */
    protected function AllowAccess(): bool
    {
        return $this->server->GetUserSession()->IsLoggedIn()
            || Configuration::Instance()->GetSectionKey(ConfigSection::PRIVACY, ConfigKeys::PRIVACY_VIEW_RESERVATIONS, new BooleanConverter());
    }

    public function GetScheduleId(): ?int
    {
        $val = $this->GetQuerystring(QueryStringKeys::SCHEDULE_ID);
        if (!empty($val)) {
            return intval($val);
        }

        return null;
    }

    public function GetResourceIds(): ?array
    {
        $val = $this->GetQuerystring(QueryStringKeys::RESOURCE_ID);
        if (!empty($val)) {
            if (!is_array($val)) {
                $val = [$val];
            }
            return array_map('intval', $val);
        }

        return null;
    }

    public function GetResourcePublicId(): ?string
    {
        $val = $this->GetQuerystring(QueryStringKeys::PUBLIC_ID);
        if (!empty($val)) {
            return $val;
        }

        return null;
    }

    public function GetReferenceNumber(): ?string
    {
        $val = $this->GetQuerystring(QueryStringKeys::REFERENCE_NUMBER);
        if (!empty($val)) {
            return $val;
        }

        return null;
    }

    public function GetSourceReferenceNumber(): ?string
    {
        $val = $this->GetQuerystring(QueryStringKeys::SOURCE_REFERENCE_NUMBER);
        if (!empty($val)) {
            return $val;
        }

        return null;
    }

    public function GetReservationDate(): ?string
    {
        $val = $this->GetQuerystring(QueryStringKeys::RESERVATION_DATE);
        if (!empty($val)) {
            return $val;
        }

        return null;
    }

    public function GetStartDate(): ?string
    {
        $val = $this->GetQuerystring(QueryStringKeys::START_DATE);
        if (!empty($val)) {
            return $val;
        }

        return null;
    }

    public function GetEndDate(): ?string
    {
        $val = $this->GetQuerystring(QueryStringKeys::END_DATE);
        if (!empty($val)) {
            return $val;
        }

        return null;
    }

    public function GetUploads(): array
    {
        return $this->server->GetFiles("files");
    }

    public function GetApiReferenceNumber(): ?string
    {
        return $this->GetForm("referenceNumber");
    }

    public function GetRequest() {
        return json_decode($this->GetRawForm('request'));
    }
}

