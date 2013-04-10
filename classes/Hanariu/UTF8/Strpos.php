<?php namespace Hanariu\UTF8;

class Strpos{

	public static function _strpos($str, $search, $offset = 0)
	{
		$offset = (int) $offset;

		if (\Hanariu\UTF8::is_ascii($str) AND \Hanariu\UTF8::is_ascii($search))
			return strpos($str, $search, $offset);

		if ($offset == 0)
		{
			$array = explode($search, $str, 2);
			return isset($array[1]) ? \Hanariu\UTF8::strlen($array[0]) : FALSE;
		}

		$str = \Hanariu\UTF8::substr($str, $offset);
		$pos = \Hanariu\UTF8::strpos($str, $search);
		return ($pos === FALSE) ? FALSE : ($pos + $offset);
	}

}
