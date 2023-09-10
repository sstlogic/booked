<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Domain/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Reporting/namespace.php');
require_once(ROOT_DIR . 'lib/Email/Messages/ReportEmailMessage.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');

class SendReportsJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('send-reports', 5);
    }

    protected function Execute()
    {
        $emailEnabled = Configuration::Instance()->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter());
        if (!$emailEnabled) {
            return;
        }

        $reportingRepository = new ReportingRepository();
        $userRepository = new UserRepository();
        $attributeRepository = new AttributeRepository();
        $scheduleRepository = new ScheduleRepository();
        $reportService = new ReportingService($reportingRepository, $attributeRepository, $scheduleRepository);
        $now = Date::Now();

        $scheduledReports = $reportingRepository->LoadScheduledSavedReports();

        foreach ($scheduledReports as $report) {
            if (!$report->ShouldSend($now)) {
                continue;
            }

            $user = $userRepository->LoadById($report->OwnerId());
            $sendAs = new UserSession($user->Id());
            $sendAs->FirstName = $user->FirstName();
            $sendAs->LastName = $user->LastName();
            $sendAs->Email = $user->EmailAddress();
            $sendAs->LanguageCode = $user->Language();
            $sendAs->Timezone = $user->Timezone();

            $generatedReport = $reportService->GenerateSavedReport($report->Id(), $user->Id(), $user->Timezone());
            $definition = new ReportDefinition($generatedReport, $user->Timezone());

            foreach ($report->ReportSchedule()->emails as $to) {
                Log::Debug('Sending report', ['reportName' => $report->ReportName(), 'to' => $to]);
                ServiceLocator::GetEmailService()->Send(new ReportEmailMessage($generatedReport, $definition, $to, $sendAs, ""));
            }

            $report->WithLastSentDate($now);
            $reportingRepository->UpdateSavedReport($report);
        }
    }
}

$sendReportsJob = new SendReportsJob();
$sendReportsJob->Run();