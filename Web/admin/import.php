<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

die('');
define('ROOT_DIR', '../../');

require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');

class ImportPage extends AdminPage
{

    public function __construct()
    {
        parent::__construct('Import', 1);
    }

    public function PageLoad()
    {
        $this->Display('Import/import.tpl');
    }

    public function SetJsonResponse($objectToSerialize, $error = null, $httpResponseCode = 200)
    {
    }
}

$page = new ImportPage();
$page->PageLoad();