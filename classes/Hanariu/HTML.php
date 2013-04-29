<?php namespace Hanariu;

abstract class HTML {


	public static $attribute_order = array
	(
		'action',
		'method',
		'type',
		'id',
		'name',
		'value',
		'href',
		'src',
		'width',
		'height',
		'cols',
		'rows',
		'size',
		'maxlength',
		'rel',
		'media',
		'accept-charset',
		'accept',
		'tabindex',
		'accesskey',
		'alt',
		'title',
		'class',
		'style',
		'selected',
		'checked',
		'readonly',
		'disabled',
	);


	public static $strict = TRUE;
	public static $windowed_urls = FALSE;


	public static function chars($value, $double_encode = TRUE)
	{
		return \htmlspecialchars( (string) $value, ENT_QUOTES, Hanariu::$charset, $double_encode);
	}

	public static function entities($value, $double_encode = TRUE)
	{
		return \htmlentities( (string) $value, ENT_QUOTES, Hanariu::$charset, $double_encode);
	}

	public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = TRUE)
	{
		if ($title === NULL)
		{
			$title = $uri;
		}

		if ($uri === '')
		{
			$uri = \Hanariu\URL::base($protocol, $index);
		}
		else
		{
			if (strpos($uri, '://') !== FALSE)
			{
				if (\Hanariu\HTML::$windowed_urls === TRUE AND empty($attributes['target']))
				{
					$attributes['target'] = '_blank';
				}
			}
			elseif ($uri[0] !== '#')
			{
				$uri = \Hanariu\URL::site($uri, $protocol, $index);
			}
		}

		$attributes['href'] = $uri;

		return '<a'.\Hanariu\HTML::attributes($attributes).'>'.$title.'</a>';
	}


	public static function file_anchor($file, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if ($title === NULL)
		{
			$title = \basename($file);
		}

		$attributes['href'] = \Hanariu\URL::site($file, $protocol, $index);
		return '<a'.\Hanariu\HTML::attributes($attributes).'>'.$title.'</a>';
	}


	public static function mailto($email, $title = NULL, array $attributes = NULL)
	{
		if ($title === NULL)
		{
			$title = $email;
		}

		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$email.'"'.\Hanariu\HTML::attributes($attributes).'>'.$title.'</a>';
	}


	public static function style($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if (\strpos($file, '://') === FALSE)
		{
			$file = \Hanariu\URL::site($file, $protocol, $index);
		}

		$attributes['href'] = $file;
		$attributes['rel'] = empty($attributes['rel']) ? 'stylesheet' : $attributes['rel'];
		$attributes['type'] = 'text/css';

		return '<link'.\Hanariu\HTML::attributes($attributes).' />';
	}


	public static function script($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if (\strpos($file, '://') === FALSE)
		{
			$file = \Hanariu\URL::site($file, $protocol, $index);
		}

		$attributes['src'] = $file;
		$attributes['type'] = 'text/javascript';
		return '<script'.\Hanariu\HTML::attributes($attributes).'></script>';
	}


	public static function image($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if (\strpos($file, '://') === FALSE)
		{
			$file = \Hanariu\URL::site($file, $protocol, $index);
		}

		$attributes['src'] = $file;
		return '<img'.\Hanariu\HTML::attributes($attributes).' />';
	}


	public static function attributes(array $attributes = NULL)
	{
		if (empty($attributes))
			return '';

		$sorted = array();
		foreach (\Hanariu\HTML::$attribute_order as $key)
		{
			if (isset($attributes[$key]))
			{
				$sorted[$key] = $attributes[$key];
			}
		}

		$attributes = $sorted + $attributes;

		$compiled = '';
		foreach ($attributes as $key => $value)
		{
			if ($value === NULL)
			{
				continue;
			}

			if (\is_int($key))
			{
				$key = $value;

				if ( ! \Hanariu\HTML::$strict)
				{
					$value = FALSE;
				}
			}

			$compiled .= ' '.$key;

			if ($value OR \Hanariu\HTML::$strict)
			{
				$compiled .= '="'.\Hanariu\HTML::chars($value).'"';
			}
		}

		return $compiled;
	}

}
