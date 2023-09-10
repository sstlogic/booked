<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Api/ReportsApiPage.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');

class ReportsApiPresenter extends ActionPresenter
{
    /**
     * @var IReportsApiPage
     */
    private $page;
    /**
     * @var UserSession
     */
    private $session;
    /**
     * @var IReportingRepository
     */
    private $reportingRepository;
    /**
     * @var IUserRepository
     */
    private $userRepository;

    public function __construct(IReportsApiPage $page, UserSession $session, IReportingRepository $reportingRepository, IUserRepository $userRepository)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->session = $session;
        $this->reportingRepository = $reportingRepository;
        $this->userRepository = $userRepository;

        $this->AddApi('load', 'LoadReport');
        $this->AddApi('saveSchedule', 'SaveReportSchedule');
    }

    public function LoadReport(): ApiActionResult
    {
        $id = $this->page->GetId();

        $report = $this->reportingRepository->LoadSavedReportForUser($id, $this->session->UserId);
        return new ApiActionResult(true, ReportApiDto::FromReport($report, $this->session->Email, $this->session->Timezone));
    }

    public function SaveReportSchedule($json): ApiActionResult
    {
        /** @var ReportApiDto $request */
        $request = $json;

        if (empty($request->scheduleDetails) || $request->scheduleDetails->frequency == ReportFrequency::Never) {
            $this->reportingRepository->DeleteCustomReportSchedule($request->id);
        } else {
            $schedule = ReportScheduleDetailsApiDto::FromRequest($request->scheduleDetails, $this->session->Timezone);
            $report = $this->reportingRepository->LoadSavedReportForUser($request->id, $this->session->UserId);
            $report->UpdateSchedule($schedule);

            $this->reportingRepository->UpdateSavedReport($report);
        }

        $report = $this->reportingRepository->LoadSavedReportForUser($request->id, $this->session->UserId);
        return new ApiActionResult(true, ReportApiDto::FromReport($report, $this->session->Email, $this->session->Timezone));
    }

    /**
     * @param RoleLevel[] $roles
     * @return bool
     */
    public function IsInRole($roles)
    {
        $user = $this->userRepository->LoadById($this->session->UserId);
        foreach ($roles as $role) {
            if ($user->IsInRole($role)) {
                return true;
            }
        }
        return false;
    }
}