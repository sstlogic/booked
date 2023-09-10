<?php
/**
 * Copyright 2016-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');

class SessionCleanupJob extends BookedJob
{
    public function __construct()
    {
        parent::__construct('session-cleanup', 1440);
    }

    protected function Execute()
    {
        $apiEnabled = Configuration::Instance()->GetSectionKey(ConfigSection::API, ConfigKeys::API_ENABLED, new BooleanConverter());
        if (!$apiEnabled) {
            return;
        }

        $userSessionRepository = new UserSessionRepository();
        $userSessionRepository->CleanUp();
        Log::Debug('Cleaning up stale user sessions');
    }
}

$sessionCleanupJob = new SessionCleanupJob();
$sessionCleanupJob->Run();