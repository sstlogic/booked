<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
 */

class ServiceLocator
{
    /**
     * @var Database
     */
    private static $_database = null;

    /**
     * @var Server
     */
    private static $_server = null;

    /**
     * @var IEmailService
     */
    private static $_emailService = null;

	/**
	 * @var \Booked\IFileSystem
	 */
	private static $_fileSystem = null;

    /**
     * @var ISmsService
     */
    private static $_smsService =null;

    /**
     * @return Database
     */
    public static function GetDatabase()
    {
        require_once(ROOT_DIR . 'lib/Database/namespace.php');

        if (self::$_database == null)
        {
            self::$_database = DatabaseFactory::GetDatabase();
        }
        return self::$_database;
    }

    public static function SetDatabase(Database $database)
    {
        self::$_database = $database;
    }

    /**
     * @return Server
     */
    public static function GetServer()
    {
        require_once(ROOT_DIR . 'lib/Server/namespace.php');

        if (self::$_server == null)
        {
            self::$_server = new Server();
        }
        return self::$_server;
    }

    public static function SetServer(Server $server)
    {
        self::$_server = $server;
    }

	/**
	 * @static
	 * @return IEmailService
	 */
	public static function GetEmailService()
    {
        require_once(ROOT_DIR . 'lib/Email/namespace.php');

        if (self::$_emailService == null)
        {
            if (Configuration::Instance()->GetKey(ConfigKeys::ENABLE_EMAIL, new BooleanConverter()))
            {
                self::$_emailService = new EmailService();
//                self::$_emailService = new EmailLogger();
            }
            else
            {
                self::$_emailService = new NullEmailService();
            }
        }
        return self::$_emailService;
    }

    public static function SetEmailService(IEmailService $emailService)
    {
        self::$_emailService = $emailService;
    }

	/**
	 * @static
	 * @return \Booked\FileSystem
	 */
	public static function GetFileSystem()
	{
		require_once(ROOT_DIR . 'lib/FileSystem/namespace.php');

		if (self::$_fileSystem == null)
		{
			self::$_fileSystem = new \Booked\FileSystem();
		}

		return self::$_fileSystem;
	}

	public static function SetFileSystem(\Booked\IFileSystem $fileSystem)
	{
		self::$_fileSystem = $fileSystem;
	}

    public static function GetSmsService()
    {
        require_once(ROOT_DIR . 'lib/SMS/namespace.php');
        if (self::$_smsService == null)
        {
            self::$_smsService = new SmsService();
        }

        return self::$_smsService;
    }

    public static function SetSmsService(ISmsService $fileSystem)
    {
        self::$_smsService = $fileSystem;
    }

}
