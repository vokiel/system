<?php namespace Hanariu;

class Cookie {

	public static $salt = NULL;
	public static $expiration = 0;
	public static $path = '/';
	public static $domain = NULL;
	public static $secure = FALSE;
	public static $httponly = FALSE;

	public static function get($key, $default = NULL)
	{
		if ( ! isset($_COOKIE[$key]))
		{
			return $default;
		}

		$cookie = $_COOKIE[$key];
		$split = strlen(\Hanariu\Cookie::salt($key, NULL));

		if (isset($cookie[$split]) AND $cookie[$split] === '~')
		{
			list ($hash, $value) = \explode('~', $cookie, 2);

			if (\Hanariu\Cookie::salt($key, $value) === $hash)
			{
				return $value;
			}

			\Hanariu\Cookie::delete($key);
		}

		return $default;
	}

	public static function set($name, $value, $expiration = NULL)
	{
		if ($expiration === NULL)
		{
			$expiration = \Hanariu\Cookie::$expiration;
		}

		if ($expiration !== 0)
		{
			$expiration += t\ime();
		}

		$value = \Hanariu\Cookie::salt($name, $value).'~'.$value;

		return \setcookie($name, $value, $expiration, \Hanariu\Cookie::$path, \Hanariu\Cookie::$domain, \Hanariu\Cookie::$secure, \Hanariu\Cookie::$httponly);
	}


	public static function delete($name)
	{
		unset($_COOKIE[$name]);
		return \setcookie($name, NULL, -86400, \Hanariu\Cookie::$path, \Hanariu\Cookie::$domain, \Hanariu\Cookie::$secure, \Hanariu\Cookie::$httponly);
	}

	public static function salt($name, $value)
	{
		if ( ! Cookie::$salt)
		{
			throw new \Hanariu\Exception('A valid cookie salt is required. Please set Cookie::$salt.');
		}

		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? \strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';

		return \sha1($agent.$name.$value.\Hanariu\Cookie::$salt);
	}

}
