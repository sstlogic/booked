<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class EmbeddedEmailImage
{
    public $Contents;
    public $Cid;

    /**
     * @param string $contents
     * @param string $cid
     */
    public function __construct($contents, $cid)
    {
        $this->Contents = $contents;
        $this->Cid = $cid;
    }
}
