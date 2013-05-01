<?php namespace Hanariu\UTF8;

class Strrpos{

	public static function _strrpos($str, $search, $offset = 0)
	{
		$offset = (int) $offset;

		if (\Hanariu\UTF8::is_ascii($str) AND \Hanariu\UTF8::is_ascii($search))
			return \strrpos($str, $search, $offset);

		if ($offset == 0)
		{
			$array = \explode($search, $str, -1);
			return isset($array[0]) ? \Hanariu\UTF8::strlen(\implode($search, $array)) : FALSE;
		}

		$str = \Hanariu\UTF8::substr($str, $offset);
		$pos = \Hanariu\UTF8::strrpos($str, $search);
		return ($pos === FALSE) ? FALSE : ($pos + $offset);
	}

}
