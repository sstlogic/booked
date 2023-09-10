<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class CustomFileCache {
    public $css = '';
    public $logo = '';
    public $favicon = '';

    /**
     * @return CustomFileCache
     */
    public static function Load($path)
    {
        $contents = @file_get_contents("{$path}custom-cache.json");

        if (!$contents) {
            $val = json_encode(new CustomFileCache());
        } else {
            $val = $contents;
        }

        $decoded = json_decode($val);

        $cache = new CustomFileCache();
        $cache->css = $decoded->css;
        $cache->logo = $decoded->logo;
        $cache->favicon = $decoded->favicon;

        return $cache;
    }

    public function Save($path) {
        $json = json_encode($this);
        file_put_contents("{$path}custom-cache.json", $json);
    }

    public function UpdateLogo()
    {
        $this->logo = time();
    }

    public function UpdateCss()
    {
        $this->css = time();
    }

    public function UpdateFavicon()
    {
        $this->favicon = time();
    }
}