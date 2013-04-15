<?php namespace Hanariu\Core;

class Handler {

	public static function error_handler($code, $error, $file = NULL, $line = NULL)
	{
		if (error_reporting() & $code)
		{
			throw new \ErrorException($error, $code, 0, $file, $line);
		}
		return TRUE;
	}

	public static function shutdown_handler()
	{
		if ( ! \Hanariu\Hanariu::$_init)
		{
			return;
		}

		try
		{
			if (\Hanariu\Hanariu::$caching === TRUE AND \Hanariu\Hanariu::$_files_changed === TRUE)
			{
				Cache::cache('\Hanariu\Hanariu::find_file()', \Hanariu\Hanariu::$_files);
			}
		}
		catch (\Exception $e)
		{
			\Hanariu\Exception::handler($e);
		}

		if (\Hanariu\Hanariu::$errors AND $error = error_get_last() AND in_array($error['type'], \Hanariu\Hanariu::$shutdown_errors))
		{
			ob_get_level() AND ob_clean();
			\Hanariu\Exception::handler(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
			exit(1);
		}
	}

}
