<?php namespace Hanariu;

class Security {

	public static $token_name = 'security_token';

	public static function token($new = FALSE)
	{
		$session = Session::instance();
		$token = $session->get(Security::$token_name);

		if ($new === TRUE OR ! $token)
		{
			$token = sha1(uniqid(NULL, TRUE));
			$session->set(Security::$token_name, $token);
		}

		return $token;
	}

	public static function check($token)
	{
		return Security::token() === $token;
	}

	public static function strip_image_tags($str)
	{
		return preg_replace('#<img\s.*?(?:src\s*=\s*["\']?([^"\'<>\s]*)["\']?[^>]*)?>#is', '$1', $str);
	}

	public static function encode_php_tags($str)
	{
		return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
	}

}
