<?php namespace Hanariu\UTF8;

class Strcasecmp{

	public static function _strcasecmp($str1, $str2)
	{
		if (\Hanariu\UTF8::is_ascii($str1) AND \Hanariu\UTF8::is_ascii($str2))
			return \strcasecmp($str1, $str2);

		$str1 = \Hanariu\UTF8::strtolower($str1);
		$str2 = \Hanariu\UTF8::strtolower($str2);
		return \strcmp($str1, $str2);
	}

}
