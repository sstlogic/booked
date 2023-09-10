<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Page.php');
require_once(ROOT_DIR . 'Domain/Access/namespace.php');

class AccessoryQRRouterPage extends Page
{
    public function __construct()
    {
        parent::__construct();
    }

    public function PageLoad()
    {
        $publicId = $this->GetQuerystring(QueryStringKeys::PUBLIC_ID);

        $page = sprintf('%s/%s?%s=%s', Configuration::Instance()->GetScriptUrl(), UrlPaths::RESERVATION, QueryStringKeys::ACCESSORY_ID, $publicId);

        $this->Redirect($page);
    }
}