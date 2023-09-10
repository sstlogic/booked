<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/Config/namespace.php'); // namespace.php is an include files of classes

class PluginManager
{
	/**
	 * @var PluginManager
	 */
	private static $_instance = null;

	private $cache = array();

	private function __construct()
	{
	}

	/**
	 * @static
	 * @return PluginManager
	 */
	public static function Instance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new PluginManager();
		}
		return self::$_instance;
	}

	/**
	 * @static
	 * @param $pluginManager PluginManager
	 * @return void
	 */
	public static function SetInstance($pluginManager)
	{
		self::$_instance = $pluginManager;
	}

	/**
	 * @return IAuthentication the authorization class to use
	 */
	public function LoadAuthentication()
	{
		require_once(ROOT_DIR . 'lib/Application/Authentication/namespace.php');
		require_once(ROOT_DIR . 'Domain/Access/namespace.php');
		$authentication = new Authentication($this->LoadAuthorization(), new UserRepository(), new GroupRepository());
		$plugin = $this->LoadPlugin(ConfigKeys::PLUGIN_AUTHENTICATION, 'Authentication', $authentication);

		if (!is_null($plugin))
		{
			return $plugin;
		}

		return $authentication;
	}

	/**
	 * @return IPermissionService
	 */
	public function LoadPermission()
	{
		require_once(ROOT_DIR . 'lib/Application/Authorization/namespace.php');

		$resourcePermissionStore = new ResourcePermissionStore(new ScheduleUserRepository());
		$permissionService = new PermissionService($resourcePermissionStore);

		$plugin = $this->LoadPlugin(ConfigKeys::PLUGIN_PERMISSION, 'Permission', $permissionService);

		if (!is_null($plugin))
		{
			return $plugin;
		}

		return $permissionService;
	}

	/**
	 * @return IAuthorizationService
	 */
	public function LoadAuthorization()
	{
		require_once(ROOT_DIR . 'lib/Application/Authorization/namespace.php');

		$authorizationService = new AuthorizationService(new UserRepository());

		$plugin = $this->LoadPlugin(ConfigKeys::PLUGIN_AUTHORIZATION, 'Authorization', $authorizationService);

		if (!is_null($plugin))
		{
			return $plugin;
		}

		return $authorizationService;
	}

	/**
	 * @return IPreReservationFactory
	 */
	public function LoadPreReservation()
	{
		require_once(ROOT_DIR . 'lib/Application/Reservation/Validation/namespace.php');

		$factory = new PreReservationFactory();

		$plugin = $this->LoadPlugin(ConfigKeys::PLUGIN_PRERESERVATION, 'PreReservation', $factory);

		if (!is_null($plugin))
		{
			return $plugin;
		}

		return $factory;
	}

	/**
	 * @return IPostReservationFactory
	 */
	public function LoadPostReservation()
	{
		require_once(ROOT_DIR . 'lib/Application/Reservation/Notification/namespace.php');

		$factory = new PostReservationFactory();

		$plugin = $this->LoadPlugin(ConfigKeys::PLUGIN_POSTRESERVATION, 'PostReservation', $factory);

		if (!is_null($plugin))
		{
			return $plugin;
		}

		return $factory;
	}

	/**
	 * @return IPostRegistration
	 */
	public function LoadPostRegistration()
	{
		require_once(ROOT_DIR . 'lib/Application/Authorization/namespace.php');

		$userRepository = new UserRepository();
		$postRegistration = new PostRegistration(new WebAuthentication(self::LoadAuthentication()), new AccountActivation($userRepository, $userRepository));

		$plugin = $this->LoadPlugin(ConfigKeys::PLUGIN_POSTREGISTRATION, 'PostRegistration', $postRegistration);

		if (!is_null($plugin))
		{
			return $plugin;
		}

		return $postRegistration;
	}

	/**
	 * @param string $configKey key to use
	 * @param string $pluginSubDirectory subdirectory name under 'plugins'
	 * @param mixed $baseImplementation the base implementation of the plugin.  allows decorating
	 * @return mixed|null plugin implementation
	 */
	private function LoadPlugin($configKey, $pluginSubDirectory, $baseImplementation)
	{
		if (!$this->Cached($configKey))
		{
			$plugin = Configuration::Instance()->GetSectionKey(ConfigSection::PLUGINS, $configKey);
			$pluginFile = ROOT_DIR . "plugins/$pluginSubDirectory/$plugin/$plugin.php";

			if (!empty($plugin) && file_exists($pluginFile))
			{
				try
				{
					Log::Debug('Loading plugin.', ['type' => $configKey, 'plugin' => $plugin]);
					require_once($pluginFile);
					$this->Cache($configKey, new $plugin($baseImplementation));
				} catch (Exception $ex)
				{
					Log::Error('Error loading plugin.', ['type' => $configKey, 'plugin' => $plugin, 'exception' => $ex]);
				}
			}
			else
			{
				$this->Cache($configKey, null);
			}
		}
		return $this->GetCached($configKey);

	}

	private function Cached($cacheKey)
	{
		return array_key_exists($cacheKey, $this->cache);
	}

	private function Cache($cacheKey, $object)
	{
		$this->cache[$cacheKey] = $object;
	}

	private function GetCached($cacheKey)
	{
		return $this->cache[$cacheKey];
	}
}