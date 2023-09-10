<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

abstract class BookedJob
{
    protected $name;
    protected $interval;

    public function __construct($name, $interval)
    {
        $this->name = $name;
        $this->interval = $interval;
    }

    public function Run()
    {
        try {
            JobCop::EnsureCommandLine();
            Log::Debug('Running scheduled job.', ['jobName' => $this->name]);

            if (JobCop::EnforceSchedule($this->name, $this->interval)) {
                $this->Execute();
                JobCop::UpdateLastRun( $this->name, true);
            }

        } catch (Throwable $ex) {
            Log::Error('Error running scheduled job.', ['jobName' => $this->name, 'exception' => $ex]);
            JobCop::UpdateLastRun( $this->name, false);
        }

        Log::Debug('Finished running scheduled job.', ['jobName' => $this->name]);
    }

    protected abstract function Execute();
}