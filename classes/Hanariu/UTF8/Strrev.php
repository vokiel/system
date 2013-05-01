<?php namespace Hanariu\UTF8;

class Strrev{

	public static function _strrev($str)
	{
		if (\Hanariu\UTF8::is_ascii($str))
			return \strrev($str);

		\preg_match_all('/./us', $str, $matches);
		return \implode('', \array_reverse($matches[0]));
	}

}
