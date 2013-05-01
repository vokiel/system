<?php namespace Hanariu\UTF8;

class Ltrim{

	public static function _ltrim($str, $charlist = NULL)
	{
		if ($charlist === NULL)
			return \ltrim($str);

		if (\Hanariu\UTF8::is_ascii($charlist))
			return \ltrim($str, $charlist);

		$charlist = \preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);

		return \preg_replace('/^['.$charlist.']+/u', '', $str);
	}

}
