<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

namespace Booked;

require_once(ROOT_DIR . 'lib/Common/namespace.php');
require_once(ROOT_DIR . 'lib/Application/Admin/namespace.php');

use Log;
use Paths;
use TemplateCacheDirectory;

interface IFileSystem
{
    /**
     * @param $path string
     * @param $fileName string
     * @param $fileContents string
     * @return bool
     */
    public function Save($path, $fileName, $fileContents);

    /**
     * @param $fullPath string
     * @return string
     */
    public function GetFileContents($fullPath);

    /**
     * @param $fullPath string
     * @return bool
     */
    public function RemoveFile($fullPath);

    /**
     * @return string
     */
    public function GetReservationAttachmentsPath();

    /**
     * @param $fullPath string
     * @return string[]
     */
    public function GetFiles($fullPath);

    /**
     * @param string $fullPath
     * @return bool
     */
    public function Exists($fullPath);

    /**
     * @return void
     */
    public function FlushSmartyCache();

}

class FileSystem implements IFileSystem
{
    public function Save($path, $fileName, $fileContents)
    {
        $fullName = $path . $fileName;
        Log::Debug('Saving file', ['fileName' => $fullName]);

        if (!is_dir($path)) {
            Log::Debug('Directory does not exist, creating', ['path' => $path]);
            if (mkdir($path, 0755)=== false) {
                Log::Error('Could create directory', ['path' => $path]);
                return false;
            }
        }

        if (file_put_contents($fullName, $fileContents) === false) {
            Log::Error('Could not write contents of file', ['fileName' => $fullName]);
            return false;
        }

        return true;
    }

    public function GetFileContents($fullPath)
    {
        $contents = file_get_contents($fullPath);
        if ($contents === false) {
            Log::Error('Could not read contents of file', ['path' => $fullPath]);
            return null;
        }

        return $contents;
    }

    public function RemoveFile($fullPath)
    {
        Log::Debug('Deleting file', ['path' => $fullPath]);
        if (unlink($fullPath) === false) {
            Log::Error('Could not delete file', ['path' => $fullPath]);
            return false;
        }

        return true;
    }

    public function GetReservationAttachmentsPath()
    {
        return Paths::ReservationAttachments();
    }

    public function GetResourceMapPath()
    {
        return Paths::ResourceMaps();
    }

    public function GetFiles($path, $filter = '*')
    {
        return glob($path . $filter);
    }

    public function Exists($fullPath)
    {
        return file_exists($fullPath);
    }

    public function FlushSmartyCache()
    {
        $cacheDir = new TemplateCacheDirectory();
        $cacheDir->Flush();
    }
}