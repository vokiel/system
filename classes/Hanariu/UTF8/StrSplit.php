<?php namespace Hanariu\UTF8;

class StrSplit{

	public static function _str_split($str, $split_length = 1)
	{
		$split_length = (int) $split_length;

		if (\Hanariu\UTF8::is_ascii($str))
			return str_split($str, $split_length);

		if ($split_length < 1)
			return FALSE;

		if (\Hanariu\UTF8::strlen($str) <= $split_length)
			return array($str);

		preg_match_all('/.{'.$split_length.'}|[^\x00]{1,'.$split_length.'}$/us', $str, $matches);

		return $matches[0];
	}

}
