<?php namespace Hanariu;

class Hanariu {

	const VERSION  = '1.0.1';
	const PRODUCTION  = 10;
	const STAGING     = 20;
	const TESTING     = 30;
	const DEVELOPMENT = 40;
	const FILE_CACHE = ":header \n\n// :name\n\n:data\n";
	public static $environment = Hanariu::DEVELOPMENT;
	public static $is_windows = FALSE;
	public static $magic_quotes = FALSE;
	public static $safe_mode = FALSE;
	public static $content_type = 'text/html';
	public static $charset = 'utf-8';
	public static $server_name = '';
	public static $hostnames = array();
	public static $base_url = '/';
	public static $index_file = 'index.php';
	public static $cache_dir;
	public static $cache_life = 60;
	public static $caching = FALSE;
	public static $profiling = TRUE;
	public static $errors = TRUE;
	public static $shutdown_errors = array(E_PARSE, E_ERROR, E_USER_ERROR);
	public static $expose = FALSE;
	public static $log;
	public static $config;
	public static $_init = FALSE;
	public static $_modules = array();
	public static $_apps = array();
	public static $_paths = array(APPPATH, SYSPATH);
	public static $_files = array();
	public static $_files_changed = FALSE;

	public static function init(array $settings = NULL)
	{
		if (Hanariu::$_init)
		{
			return;
		}

		Hanariu::$_init = TRUE;

		if (isset($settings['profile']))
		{
			Hanariu::$profiling = (bool) $settings['profile'];
		}

		\ob_start();

		if (isset($settings['errors']))
		{
			Hanariu::$errors = (bool) $settings['errors'];
		}

		if (Hanariu::$errors === TRUE)
		{
			\set_exception_handler(array('Hanariu\\Exception', 'handler'));
			\set_error_handler(array('Hanariu\\Core\\Handler', 'error_handler'));
		}

		if (Hanariu::$environment == Hanariu::DEVELOPMENT AND \extension_loaded('xdebug'))
		{
		    \ini_set('xdebug.collect_params', 3);
		}

		register_shutdown_function(array('Hanariu\\Core\\Handler', 'shutdown_handler'));

		if (\ini_get('register_globals'))
		{
			Hanariu::globals();
		}

		if (isset($settings['expose']))
		{
			Hanariu::$expose = (bool) $settings['expose'];
		}

		Hanariu::$is_windows = (DIRECTORY_SEPARATOR === '\\');
		Hanariu::$safe_mode = (bool) \ini_get('safe_mode');

		if (isset($settings['cache_dir']))
		{
			if ( ! \is_dir($settings['cache_dir']))
			{
				try
				{
					\mkdir($settings['cache_dir'], 0755, TRUE);
					\chmod($settings['cache_dir'], 0755);
				}
				catch (\Exception $e)
				{
					throw new \Hanariu\Exception('Could not create cache directory :dir',
						array(':dir' => \Hanariu\Debug::path($settings['cache_dir'])));
				}
			}

			Hanariu::$cache_dir = \realpath($settings['cache_dir']);
		}
		else
		{
			Hanariu::$cache_dir = APPPATH.'cache';
		}

		if ( ! is_writable(Hanariu::$cache_dir))
		{
			throw new Exception('Directory :dir must be writable',
				array(':dir' => \Hanariu\Debug::path(Hanariu::$cache_dir)));
		}

		if (isset($settings['cache_life']))
		{
			Hanariu::$cache_life = (int) $settings['cache_life'];
		}

		if (isset($settings['caching']))
		{
			Hanariu::$caching = (bool) $settings['caching'];
		}

		if (Hanariu::$caching === TRUE)
		{
			Hanariu::$_files = \Hanariu::cache('Hanariu::find_file()');
		}

		if (isset($settings['charset']))
		{
			Hanariu::$charset = \strtolower($settings['charset']);
		}

		if (function_exists('mb_internal_encoding'))
		{
			\mb_internal_encoding(Hanariu::$charset);
		}

		if (isset($settings['base_url']))
		{
			Hanariu::$base_url = \rtrim($settings['base_url'], '/').'/';
		}

		if (isset($settings['index_file']))
		{
			Hanariu::$index_file = \trim($settings['index_file'], '/');
		}

		Hanariu::$magic_quotes = (\version_compare(PHP_VERSION, '5.4') < 0 AND \get_magic_quotes_gpc());

		$_GET    = Hanariu::sanitize($_GET);
		$_POST   = Hanariu::sanitize($_POST);
		$_COOKIE = Hanariu::sanitize($_COOKIE);

		if ( ! Hanariu::$log instanceof \Hanariu\Log)
		{
			Hanariu::$log = \Hanariu\Log::instance();
		}

		if ( ! Hanariu::$config instanceof \Hanariu\Config)
		{
			Hanariu::$config = new \Hanariu\Config;
		}
	}

	public static function deinit()
	{
		if (Hanariu::$_init)
		{
			\spl_autoload_unregister(array('Hanariu', 'auto_load'));

			if (Hanariu::$errors)
			{
				\restore_error_handler();
				\restore_exception_handler();
			}

			Hanariu::$log = Hanariu::$config = NULL;
			Hanariu::$_modules = Hanariu::$_files = array();
			Hanariu::$_paths = array(APPPATH, SYSPATH);
			Hanariu::$_files_changed = FALSE;
			Hanariu::$_init = FALSE;
		}
	}

	public static function globals()
	{
		if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS']))
		{
			echo "Global variable overload attack detected! Request aborted.\n";
			exit(1);
		}

		$global_variables = \array_keys($GLOBALS);
		$global_variables = \array_diff($global_variables, array(
			'_COOKIE',
			'_ENV',
			'_GET',
			'_FILES',
			'_POST',
			'_REQUEST',
			'_SERVER',
			'_SESSION',
			'GLOBALS',
		));

		foreach ($global_variables as $name)
		{
			unset($GLOBALS[$name]);
		}
	}

	public static function sanitize($value)
	{
		if (\is_array($value) OR \is_object($value))
		{
			foreach ($value as $key => $val)
			{
				$value[$key] = Hanariu::sanitize($val);
			}
		}
		elseif (\is_string($value))
		{
			if (Hanariu::$magic_quotes === TRUE)
			{
				$value = \stripslashes($value);
			}

			if (\strpos($value, "\r") !== FALSE)
			{
				$value = \str_replace(array("\r\n", "\r"), "\n", $value);
			}
		}

		return $value;
	}

	public static function auto_load($class, $directory = 'classes')
	{
		$class     = \ltrim($class, '\\');
		$file      = '';
		$namespace = '';

		if ($last_namespace_position = \strripos($class, '\\'))
		{
			$namespace = \substr($class, 0, $last_namespace_position);
			$class     = \substr($class, $last_namespace_position + 1);
			$file      = \str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
		}

		$file .= \str_replace('_', DIRECTORY_SEPARATOR, $class);

		if ($path = Hanariu::find_file($directory, $file))
		{
			require $path;

			return TRUE;
		}

		return FALSE;
	}
	
	public static function modules(array $modules = NULL)
	{
		if ($modules === NULL)
		{
			return Hanariu::$_modules;
		}
		$paths = Hanariu::$_paths;

		foreach ($modules as $name => $path)
		{
			if (\is_dir($path))
			{
				$paths[] = $modules[$name] = \realpath($path).DIRECTORY_SEPARATOR;
			}
			else
			{
				throw new \Hanariu\Exception('Attempted to load an invalid or missing module \':module\' at \':path\'', array(
					':module' => $name,
					':path'   => \Hanariu\Debug::path($path),
				));
			}
		}

		$paths[] = SYSPATH;
		Hanariu::$_paths = $paths;
		Hanariu::$_modules = $modules;

		foreach (Hanariu::$_modules as $path)
		{
			$init = $path.'init'.EXT;

			if (\is_file($init))
			{
				require_once $init;
			}
		}

		return Hanariu::$_modules;
	}

	public static function include_paths()
	{
		return Hanariu::$_paths;
	}

	public static function find_file($dir, $file, $ext = NULL, $array = FALSE)
	{
		if ($ext === NULL)
		{
			$ext = EXT;
		}
		elseif ($ext)
		{
			$ext = ".{$ext}";
		}
		else
		{
			$ext = '';
		}

		$path = $dir.DIRECTORY_SEPARATOR.$file.$ext;

		if (Hanariu::$caching === TRUE AND isset(Hanariu::$_files[$path.($array ? '_array' : '_path')]))
		{
			return Hanariu::$_files[$path.($array ? '_array' : '_path')];
		}

		if (Hanariu::$profiling === TRUE AND \class_exists('\Hanariu\Profiler', FALSE))
		{
			$benchmark = \Hanariu\Profiler::start('Hanariu', __FUNCTION__);
		}

		if ($array OR $dir === 'config' OR $dir === 'i18n' OR $dir === 'messages')
		{
			$paths = \array_reverse(Hanariu::$_paths);

			$found = array();

			foreach ($paths as $dir)
			{
				if (\is_file($dir.$path))
				{
					$found[] = $dir.$path;
				}
			}
		}
		else
		{
			$found = FALSE;

			foreach (Hanariu::$_paths as $dir)
			{
				if (\is_file($dir.$path))
				{
					$found = $dir.$path;

					break;
				}
			}
		}

		if (Hanariu::$caching === TRUE)
		{
			Hanariu::$_files[$path.($array ? '_array' : '_path')] = $found;

			Hanariu::$_files_changed = TRUE;
		}

		if (isset($benchmark))
		{
			\Hanariu\Profiler::stop($benchmark);
		}

		return $found;
	}

	public static function list_files($directory = NULL, array $paths = NULL)
	{
		if ($directory !== NULL)
		{
			$directory .= DIRECTORY_SEPARATOR;
		}

		if ($paths === NULL)
		{
			$paths = Hanariu::$_paths;
		}

		$found = array();

		foreach ($paths as $path)
		{
			if (\is_dir($path.$directory))
			{
				$dir = new DirectoryIterator($path.$directory);

				foreach ($dir as $file)
				{
					$filename = $file->getFilename();

					if ($filename[0] === '.' OR $filename[\strlen($filename)-1] === '~')
					{
						continue;
					}

					// Relative filename is the array key
					$key = $directory.$filename; //$key = $directory.'/'.$filename;?

					if ($file->isDir())
					{
						if ($sub_dir = Hanariu::list_files($key, $paths))
						{
							if (isset($found[$key]))
							{
								$found[$key] += $sub_dir;
							}
							else
							{
								$found[$key] = $sub_dir;
							}
						}
					}
					else
					{
						if ( ! isset($found[$key]))
						{
							$found[$key] = \realpath($file->getPathName());
						}
					}
				}
			}
		}

		\ksort($found);

		return $found;
	}

	public static function load($file)
	{
		return include $file;
	}

	public static function cache($name, $data = NULL, $lifetime = NULL)
	{
		// Cache file is a hash of the name
		$file = \sha1($name).'.txt';

		// Cache directories are split by keys to prevent filesystem overload
		$dir = Hanariu::$cache_dir.DIRECTORY_SEPARATOR.$file[0].$file[1].DIRECTORY_SEPARATOR;

		if ($lifetime === NULL)
		{
			// Use the default lifetime
			$lifetime = Hanariu::$cache_life;
		}

		if ($data === NULL)
		{
			if (\is_file($dir.$file))
			{
				if ((\time() - \filemtime($dir.$file)) < $lifetime)
				{
					// Return the cache
					try
					{
						return \unserialize(\file_get_contents($dir.$file));
					}
					catch (\Exception $e)
					{
						// Cache is corrupt, let return happen normally.
					}
				}
				else
				{
					try
					{
						// Cache has expired
						\unlink($dir.$file);
					}
					catch (\Exception $e)
					{
						// Cache has mostly likely already been deleted,
						// let return happen normally.
					}
				}
			}

			// Cache not found
			return NULL;
		}

		if ( ! \is_dir($dir))
		{
			// Create the cache directory
			\mkdir($dir, 0777, TRUE);

			// Set permissions (must be manually set to fix umask issues)
			\chmod($dir, 0777);
		}

		// Force the data to be a string
		$data = \serialize($data);

		try
		{
			// Write the cache
			return (bool) \file_put_contents($dir.$file, $data, LOCK_EX);
		}
		catch (\Exception $e)
		{
			// Failed to write cache
			return FALSE;
		}
	}

	public static function version()
	{
		return 'Hanariu Framework '.Hanariu::VERSION;
	}

}
