<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'Pages/PersonalCalendarPage.php');

$page = new SecureActionPageDecorator(new PersonalCalendarPage());
if ($page->TakingAction())
{
    $page->ProcessAction();
}
else
{
    $page->PageLoad();
}


