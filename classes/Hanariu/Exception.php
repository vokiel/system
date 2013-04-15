<?php namespace Hanariu;

class Exception extends \Exception {

	public static $php_errors = array(
		E_ERROR => 'Fatal Error',
		E_USER_ERROR => 'User Error',
		E_PARSE => 'Parse Error',
		E_WARNING => 'Warning',
		E_USER_WARNING => 'User Warning',
		E_STRICT => 'Strict',
		E_NOTICE => 'Notice',
		E_RECOVERABLE_ERROR => 'Recoverable Error',
		E_DEPRECATED => 'Deprecated',
	);

	public static $error_view = 'errors/error';
	public static $error_view_content_type = 'text/html';

	public function __construct($message = "", array $variables = NULL, $code = 0, \Exception $previous = NULL)
	{
		$message = __($message, $variables);
		parent::__construct($message, (int) $code, $previous);
		$this->code = $code;
	}


	public function __toString()
	{
		return Exception::text($this);
	}


	public static function handler(\Exception $e)
	{
		$response = Exception::_handler($e);
		echo $response->send_headers()->body();
		exit(1);
	}

	public static function _handler(\Exception $e)
	{
		try
		{
			Exception::log($e);
			$response = Exception::response($e);

			return $response;
		}
		catch (Exception $e)
		{
			ob_get_level() AND ob_clean();
			header('Content-Type: text/plain; charset='.Hanariu::$charset, TRUE, 500);
			echo Exception::text($e);
			exit(1);
		}
	}

	public static function log(\Exception $e, $level = Log::EMERGENCY)
	{
		if (is_object(Hanariu::$log))
		{
			$error = Exception::text($e);
			Hanariu::$log->add($level, $error, NULL, array('exception' => $e));
			Hanariu::$log->write();
		}
	}

	public static function text(\Exception $e)
	{
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
			get_class($e), $e->getCode(), strip_tags($e->getMessage()), Debug::path($e->getFile()), $e->getLine());
	}

	public static function response(\Exception $e)
	{
		try
		{
			$class   = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();
			$trace   = $e->getTrace();

			if ( ! headers_sent())
			{
				$http_header_status = ($e instanceof HTTP\Exception) ? $code : 500;
			}

			if ($e instanceof HTTP\Exception AND $trace[0]['function'] == 'factory')
			{
				extract(array_shift($trace));
			}


			if ($e instanceof \ErrorException)
			{

				if (function_exists('xdebug_get_function_stack') AND $code == E_ERROR)
				{
					$trace = array_slice(array_reverse(xdebug_get_function_stack()), 4);

					foreach ($trace as & $frame)
					{

						if ( ! isset($frame['type']))
						{
							$frame['type'] = '??';
						}

						if (isset($frame['params']) AND ! isset($frame['args']))
						{
							$frame['args'] = $frame['params'];
						}
					}
				}
				
				if (isset(Exception::$php_errors[$code]))
				{
					$code = Exception::$php_errors[$code];
				}
			}


			if (defined('PHPUnit_MAIN_METHOD'))
			{
				$trace = array_slice($trace, 0, 2);
			}

			$view = View::factory(Exception::$error_view, get_defined_vars());
			$response = Response::factory();
			$response->status(($e instanceof HTTP\Exception) ? $e->getCode() : 500);
			$response->headers('Content-Type', Exception::$error_view_content_type.'; charset='.Hanariu::$charset);
			$response->body($view->render());
		}
		catch (Exception $e)
		{
			$response = Response::factory();
			$response->status(500);
			$response->headers('Content-Type', 'text/plain');
			$response->body(Exception::text($e));
		}

		return $response;
	}

}