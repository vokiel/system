<?php namespace Hanariu\UTF8;

class Ucwords{

	public static function _ucfirst($str)
	{
		if (\Hanariu\UTF8::is_ascii($str))
			return ucfirst($str);

		preg_match('/^(.?)(.*)$/us', $str, $matches);
		return \Hanariu\UTF8::strtoupper($matches[1]).$matches[2];
	}

}
