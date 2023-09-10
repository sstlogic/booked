<?php
/**
Copyright 2014-2023 Twinkle Toes Software, LLC
*/

class TemplateCacheDirectory
{
	public function Flush()
	{
		try
		{
			$dirName = $this->GetDirectory();
			$cacheDir = opendir($dirName);
		    while (false !== ($file = readdir($cacheDir)))
			{
		        if($file != "." && $file != "..")
				{
		            unlink($dirName . $file);
		        }
		    }
		    closedir($cacheDir);
		}
		catch(Exception $ex)
		{
			Log::Error('Could not flush template cache directory', ['exception' => $ex]);
		}
	}

	public function GetDirectory()
	{
		return ROOT_DIR . 'tpl_c/';
	}
}