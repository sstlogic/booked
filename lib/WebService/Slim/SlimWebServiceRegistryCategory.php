<?php
/**
Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class SlimWebServiceRegistryCategory
{
	private $gets = array();
	private $posts = array();
	private $deletes = array();
    private $name;
    private $baseUrl;

    public function __construct($name, $baseUrl)
	{
		$this->name = $name;
        $this->baseUrl = $baseUrl;
    }

	/**
	 * @return array|SlimServiceRegistration[]
	 */
	public function Gets()
	{
		return $this->gets;
	}

	/**
	 * @return array|SlimServiceRegistration[]
	 */
	public function Posts()
	{
		return $this->posts;
	}

	/**
	 * @return array|SlimServiceRegistration[]
	 */
	public function Deletes()
	{
		return $this->deletes;
	}

	public function AddGet($route, $callback, $routeName)
	{
		$this->gets[] = new SlimServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	public function AddPost($route, $callback, $routeName)
	{
		$this->posts[] = new SlimServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	public function AddDelete($route, $callback, $routeName)
	{
		$this->deletes[] = new SlimServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	public function AddSecureGet($route, $callback, $routeName)
	{
		$this->gets[] = new SlimSecureServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	public function AddSecurePost($route, $callback, $routeName)
	{
		$this->posts[] = new SlimSecureServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	public function AddSecureDelete($route, $callback, $routeName)
	{
		$this->deletes[] = new SlimSecureServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	public function AddAdminGet($route, $callback, $routeName)
	{
		$this->gets[] = new SlimAdminServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	public function AddAdminPost($route, $callback, $routeName)
	{
		$this->posts[] = new SlimAdminServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	public function AddAdminDelete($route, $callback, $routeName)
	{
		$this->deletes[] = new SlimAdminServiceRegistration($this->baseUrl, $this->name, $route, $callback, $routeName);
	}

	/**
	 * @return mixed
	 */
	public function Name()
	{
		return $this->name;
	}
}

