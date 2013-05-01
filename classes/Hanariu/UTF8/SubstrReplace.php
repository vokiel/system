<?php namespace Hanariu\UTF8;

class SubstrReplace{

	public static function _substr_replace($str, $replacement, $offset, $length = NULL)
	{
		if (\Hanariu\UTF8::is_ascii($str))
			return ($length === NULL) ? \substr_replace($str, $replacement, $offset) : \substr_replace($str, $replacement, $offset, $length);

		$length = ($length === NULL) ? \Hanariu\UTF8::strlen($str) : (int) $length;
		\preg_match_all('/./us', $str, $str_array);
		\preg_match_all('/./us', $replacement, $replacement_array);

		\array_splice($str_array[0], $offset, $length, $replacement_array[0]);
		return \implode('', $str_array[0]);
	}

}
