<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

@define('ROOT_DIR', dirname(__FILE__) . '/../');
require_once(ROOT_DIR . 'Jobs/JobCop.php');
require_once(ROOT_DIR . 'Jobs/JobServer.php');
require_once(ROOT_DIR . 'Jobs/BookedJob.php');

JobCop::EnsureCommandLine();

ServiceLocator::SetServer(new JobServer());

Log::Debug("Executing all scheduled jobs");

include(ROOT_DIR . 'Jobs/auto-extend.php');
include(ROOT_DIR . 'Jobs/autorelease.php');
include(ROOT_DIR . 'Jobs/replenish-credits.php');
include(ROOT_DIR . 'Jobs/sendmissedcheckin.php');
include(ROOT_DIR . 'Jobs/sendmissedcheckout.php');
include(ROOT_DIR . 'Jobs/sendreminders.php');
include(ROOT_DIR . 'Jobs/sendreports.php');
include(ROOT_DIR . 'Jobs/sendseriesend.php');
include(ROOT_DIR . 'Jobs/sendwaitlist.php');
include(ROOT_DIR . 'Jobs/sessioncleanup.php');

Log::Debug("Finished executing all scheduled jobs");