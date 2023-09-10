<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class Paths
{
	/**
	 * Filesystem directory for storing reservation attachments. Always contains trailing slash
	 *
	 * @static
	 * @return string
	 */
	public static function ReservationAttachments()
	{
		$uploadDir = Configuration::Instance()->GetSectionKey(ConfigSection::UPLOADS, ConfigKeys::UPLOAD_RESERVATION_ATTACHMENTS);

		if (empty($uploadDir))
		{
			$uploadDir = dirname(__FILE__) . '/' . ROOT_DIR . 'uploads/reservation';
		}

		if (!is_dir($uploadDir))
		{
			$uploadDir =  dirname(__FILE__) . '/' . ROOT_DIR . $uploadDir;
		}

		if (!BookedStringHelper::EndsWith($uploadDir, '/'))
		{
			$uploadDir = $uploadDir . '/';
		}

		if (!is_dir($uploadDir))
		{
			Log::Debug('Could not find directory. Attempting to create it', ['directoryName' => $uploadDir]);
			$created = mkdir($uploadDir);
			if ($created)
			{
				Log::Debug('Created directory', ['directoryName' => $uploadDir]);
			}
			else
			{
				Log::Debug('Could not create directory', ['directoryName' => $uploadDir]);
			}

		}
		return $uploadDir;
	}

    /**
	 * Filesystem directory for storing resource maps. Always contains trailing slash
	 *
	 * @static
	 * @return string
	 */
	public static function ResourceMaps()
	{
        $uploadDir = ROOT_DIR . 'uploads/maps';

		if (!is_dir($uploadDir))
		{
			$uploadDir =  ROOT_DIR . $uploadDir;
		}

		if (!BookedStringHelper::EndsWith($uploadDir, '/'))
		{
			$uploadDir = $uploadDir . '/';
		}

		if (!is_dir($uploadDir))
		{
			Log::Debug('Could not find directory. Attempting to create it', ['directoryName' => $uploadDir]);
			$created = mkdir($uploadDir);
			if ($created)
			{
				Log::Debug('Created directory', ['directoryName' => $uploadDir]);
			}
			else
			{
				Log::Debug('Could not create directory', ['directoryName' => $uploadDir]);
			}

		}
		return $uploadDir;
	}

    /**
     * Filesystem directory for storing terms of service file. Always contains trailing slash
     *
     * @static
     * @return string
     */
	public static function Terms()
    {
        return ROOT_DIR . 'Web/uploads/tos/';
    }

    /**
     * Filesystem directory for storing terms of email templates for given language. Always contains trailing slash
     *
     * @static
     * @param $language string
     * @return string
     */
    public static function EmailTemplates($language)
    {
        if (AvailableLanguages::Contains($language)) {
            return dirname(__FILE__) . '/' . ROOT_DIR . "lang/$language/";
        }
        return dirname(__FILE__) . '/' . ROOT_DIR . "lang/en_us/";
    }
}