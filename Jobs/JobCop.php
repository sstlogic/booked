<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

class JobCop
{
    public static function EnsureCommandLine()
    {
        ServiceLocator::SetServer(new JobServer());
        try {
            if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
                die('This can only be accessed via the command line');
            }
        } catch (Exception $ex) {
            Log::Error('Error in JobCop->EnsureCommandLine.', ['exception' => $ex]);
        }
    }

    /**
     * @param string $jobName
     * @param int $minInterval
     * @return bool
     */
    public static function EnforceSchedule($jobName, $minInterval)
    {
        $lastRunCommand = new AdHocCommand('SELECT * FROM `scheduled_job_status` WHERE `job_name` = @job_name');
        $lastRunCommand->AddParameter(new Parameter("@job_name", $jobName));

        $reader = ServiceLocator::GetDatabase()->Query($lastRunCommand);
        if ($row = $reader->GetRow()) {
            $lastRunTime = Date::FromDatabase($row['last_run_date']);

            $minNextRun = $lastRunTime->AddMinutes($minInterval);

            if ($minNextRun->GreaterThan(Date::Now())) {
                Log::Debug('Skipping job run.', ['jobName' => $jobName, 'lastRun' => $lastRunTime, 'minimumNextRun' => $minNextRun]);
                return false;
            }
        }

        return true;
    }

    public static function UpdateLastRun($jobName, $success)
    {
        $updateLastRunCommand = new AdHocCommand('REPLACE INTO `scheduled_job_status` (`job_name`, `last_run_date`, `status`) VALUES (@job_name, @last_run_date, @status)');
        $updateLastRunCommand->AddParameter(new Parameter("@job_name", $jobName));
        $updateLastRunCommand->AddParameter(new Parameter("@last_run_date", Date::Now()->ToDatabase()));
        $updateLastRunCommand->AddParameter(new Parameter("@status", $success ? 1 : 0));

        ServiceLocator::GetDatabase()->Execute($updateLastRunCommand);
    }
}
