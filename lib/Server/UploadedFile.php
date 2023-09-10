<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class UploadedFile
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function OriginalName()
    {
        return $this->file['name'];
    }

    /**
     * @return string
     */
    public function TemporaryName()
    {
        return $this->file['tmp_name'];
    }

    /**
     * @return string
     */
    public function MimeType()
    {
        return $this->file['type'];
    }

    /**
     * @return int total bytes
     */
    public function Size()
    {
        return $this->file['size'];
    }

    /**
     * @return string
     */
    public function Extension()
    {
        $info = pathinfo($this->OriginalName());
        return $info['extension'];
    }

    /**
     * @return string
     */
    public function Contents()
    {
        $tmpName = $this->TemporaryName();
        $fp = fopen($tmpName, 'r');
        $content = fread($fp, filesize($tmpName));
        fclose($fp);

        return trim($content);
    }

    public function IsError()
    {
        return $this->file['error'] != UPLOAD_ERR_OK;
    }

    public function Error()
    {
        $messages = array(
            UPLOAD_ERR_OK => '',
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the maximum file size',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the maximum file size',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary storage folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk, check folder permissions of configured upload directory'
        );

        return $messages[$this->file['error']];
    }

    /**
     * @static
     * @return int
     */
    public static function GetMaxSize()
    {
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        return min($max_upload, $max_post, $memory_limit);
    }

    private static function ToBytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $val = empty($val) ? 0 : intval($val);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * @return int
     */
    public static function GetMaxSizeAsBytes()
    {
        $max_upload = self::ToBytes(ini_get('upload_max_filesize'));
        $max_post = self::ToBytes(ini_get('post_max_size'));
        $memory_limit = self::ToBytes(ini_get('memory_limit'));
        return min($max_upload, $max_post, $memory_limit);
    }

    /**
     * @static
     * @return int
     */
    public static function GetMaxUploadCount()
    {
        return (int)(ini_get('max_file_uploads'));
    }
}