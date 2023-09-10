<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

define('ROOT_DIR', '../');

require_once(ROOT_DIR . 'lib/Config/namespace.php');

$url = Configuration::Instance()->GetScriptUrl();
header("Location: $url/reservation/?{$_SERVER['QUERY_STRING']}");
die();