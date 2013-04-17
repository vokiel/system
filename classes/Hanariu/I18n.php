<?php namespace Hanariu;

class I18n {

	public static $lang = 'en';
	public static $source = 'en';
	protected static $_cache = array();

	public static function lang($lang = NULL)
	{
		if ($lang)
		{
			I18n::$lang = strtolower(str_replace(array(' ', '_'), '-', $lang));
		}

		return I18n::$lang;
	}

	public static function get($string, $lang = NULL)
	{
		if ( ! $lang)
		{
			$lang = I18n::$lang;
		}

		$table = I18n::load($lang);
		return isset($table[$string]) ? $table[$string] : $string;
	}

	public static function load($lang)
	{
		if (isset(I18n::$_cache[$lang]))
		{
			return I18n::$_cache[$lang];
		}

		$table = array();
		$parts = explode('-', $lang);

		do
		{
			$path = implode(DIRECTORY_SEPARATOR, $parts);

			if ($files = Core\Filesystem::find_file('i18n', $path, NULL, TRUE))
			{
				$t = array();
				foreach ($files as $file)
				{
					$t = array_merge($t, Core\Filesystem::load($file));
				}

				$table += $t;
			}

			array_pop($parts);
		}
		while ($parts);
		return I18n::$_cache[$lang] = $table;
	}

} 
if ( ! function_exists('__'))
{
	function __($string, array $values = NULL, $lang = 'en')
	{
		if ($lang !== I18n::$lang)
		{
			$string = I18n::get($string);
		}

		return empty($values) ? $string : strtr($string, $values);
	}
}

