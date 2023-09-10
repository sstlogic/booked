<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Page.php');
require_once(ROOT_DIR . 'Domain/Access/ResourceMapsRepository.php');

class MapImagePage extends Page
{
    public function __construct()
    {
        parent::__construct('', 1);
    }

    public function PageLoad()
    {
        if (!$this->IsAuthenticated()) {
            http_response_code(401);
            die();
        }

        $mapRepo = new ResourceMapsRepository();
        $mapId = $this->GetId();
        if (empty($mapId)) {
            http_response_code(404);
            die();
        }

        $image = $mapRepo->GetImage($mapId);
        if (empty($image)) {
            http_response_code(404);
            die();
        }

        $this->ShowImage($image);
    }

    public function GetId()
    {
        return $this->GetQuerystring(QueryStringKeys::PUBLIC_ID);
    }

    private function ShowImage(ResourceMapImage $image)
    {
        ob_start();
        $contents = $image->FileContents();
        header('Content-Type: ' . $image->FileType());
        header('Content-Length: ' . strlen($contents));
        header('Cache-Control: max-age=86400');
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo $contents;
    }
}