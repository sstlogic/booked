<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/external/phpqrcode/qrlib.php');

class QRGenerator
{
    public function SavePng($url, $path)
    {
        @QRcode::png($url, $path, QR_ECLEVEL_L, 4);
    }
}