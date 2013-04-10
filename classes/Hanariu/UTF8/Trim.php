<?php namespace Hanariu\UTF8;

class Trim{

	public static function _trim($str, $charlist = NULL)
	{
		if ($charlist === NULL)
			return trim($str);

		return \Hanariu\UTF8::ltrim(\Hanariu\UTF8::rtrim($str, $charlist), $charlist);
	}

}
