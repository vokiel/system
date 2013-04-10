<?php namespace Hanariu;

class UTF8 {

	public static $server_utf8 = FALSE;
	public static $called = array();

	public static function is_mbstring()
	{
		return UTF8::$server_utf8;
	}

	public static function clean($var, $charset = NULL)
	{
		if ( ! $charset)
		{
			$charset = Hanariu::$charset;
		}

		if (is_array($var) OR is_object($var))
		{
			foreach ($var as $key => $val)
			{
				$var[self::clean($key)] = self::clean($val);
			}
		}
		elseif (is_string($var) AND $var !== '')
		{
			$var = self::strip_ascii_ctrl($var);

			if ( ! self::is_ascii($var))
			{
				$error_reporting = error_reporting(~E_NOTICE);
				$var = iconv($charset, $charset.'//IGNORE', $var);
				error_reporting($error_reporting);
			}
		}

		return $var;
	}

	public static function is_ascii($str)
	{
		if (is_array($str))
		{
			$str = implode($str);
		}

		return ! preg_match('/[^\x00-\x7F]/S', $str);
	}

	public static function strip_ascii_ctrl($str)
	{
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}

	public static function strip_non_ascii($str)
	{
		return preg_replace('/[^\x00-\x7F]+/S', '', $str);
	}

	public static function transliterate_to_ascii($str, $case = 0)
	{
		return UTF8\TransliterateToAscii::_transliterate_to_ascii($str, $case);
	}

	public static function strlen($str)
	{
		if (UTF8::$server_utf8)
			return mb_strlen($str, Hanariu::$charset);

		return UTF8\Strlen::_strlen($str);
	}

	public static function strpos($str, $search, $offset = 0)
	{
		if (UTF8::$server_utf8)
			return mb_strpos($str, $search, $offset, Hanariu::$charset);

		return UTF8\Strpos::_strpos($str, $search, $offset);
	}

	public static function strrpos($str, $search, $offset = 0)
	{
		if (UTF8::$server_utf8)
			return mb_strrpos($str, $search, $offset, Hanariu::$charset);

		return UTF8\Strrpos::_strrpos($str, $search, $offset);
	}

	public static function substr($str, $offset, $length = NULL)
	{
		if (UTF8::$server_utf8)
			return ($length === NULL)
				? mb_substr($str, $offset, mb_strlen($str), Hanariu::$charset)
				: mb_substr($str, $offset, $length, Hanariu::$charset);

		return UTF8\Substr::_substr($str, $offset, $length);
	}

	public static function substr_replace($str, $replacement, $offset, $length = NULL)
	{
		return UTF8\SubstrReplace::_substr_replace($str, $replacement, $offset, $length);
	}

	public static function strtolower($str)
	{
		if (UTF8::$server_utf8)
			return mb_strtolower($str, Hanariu::$charset);

		return UTF8\Strtolower::_strtolower($str);
	}

	public static function strtoupper($str)
	{
		if (UTF8::$server_utf8)
			return mb_strtoupper($str, Hanariu::$charset);

		return UTF8\Strtoupper::_strtoupper($str);
	}

	public static function ucfirst($str)
	{
		return UTF8\Ucfirst::_ucfirst($str);
	}

	public static function ucwords($str)
	{
		return UTF8\Ucwords::_ucwords($str);
	}

	public static function strcasecmp($str1, $str2)
	{
		return UTF8\Strcasecmp::_strcasecmp($str1, $str2);
	}

	public static function str_ireplace($search, $replace, $str, & $count = NULL)
	{
		return UTF8\StrIreplace::_str_ireplace($search, $replace, $str, $count);
	}

	public static function stristr($str, $search)
	{
		return UTF8\Stristr::_stristr($str, $search);
	}

	public static function strspn($str, $mask, $offset = NULL, $length = NULL)
	{
		return UTF8\Strspn::_strspn($str, $mask, $offset, $length);
	}

	public static function strcspn($str, $mask, $offset = NULL, $length = NULL)
	{
		return UTF8\Strcspn::_strcspn($str, $mask, $offset, $length);
	}

	public static function str_pad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT)
	{
		return UTF8\StrPad::_str_pad($str, $final_str_length, $pad_str, $pad_type);
	}


	public static function str_split($str, $split_length = 1)
	{
		return UTF8\StrSplit::_str_split($str, $split_length);
	}


	public static function strrev($str)
	{
		return UTF8\Strrev::_strrev($str);
	}

	public static function trim($str, $charlist = NULL)
	{
		return UTF8\Trim::_trim($str, $charlist);
	}


	public static function ltrim($str, $charlist = NULL)
	{
		return UTF8\Ltrim::_ltrim($str, $charlist);
	}


	public static function rtrim($str, $charlist = NULL)
	{
		return UTF8\Rtrim::_rtrim($str, $charlist);
	}

	public static function ord($chr)
	{
		return UTF8\Ord::_ord($chr);
	}


	public static function to_unicode($str)
	{
		return UTF8\ToUnicode::_to_unicode($str);
	}

	public static function from_unicode($arr)
	{
		return UTF8\FromUnicode::_from_unicode($arr);
	}

}

if (UTF8::$server_utf8 === FALSE)
{
	// Determine if this server supports UTF-8 natively
	UTF8::$server_utf8 = extension_loaded('mbstring');
}
