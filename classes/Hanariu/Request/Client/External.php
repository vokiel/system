<?php namespace Hanariu\Request\Client;

abstract class External extends \Hanariu\Request\Client {

	public static $client = 'Curl';

	public static function factory(array $params = array(), $client = NULL)
	{
		if ($client === NULL)
		{
			$client = \Hanariu\External::$client;
		}

		$client = new $client($params);

		if ( ! $client instanceof \Hanariu\External)
		{
			throw new \Hanariu\Exception('Selected client is not a Request_Client_External object.');
		}

		return $client;
	}

	protected $_options = array();

	public function execute_request(\Hanariu\Request $request, \Hanariu\Response $response)
	{
		if (Hanariu::$profiling)
		{
			$benchmark = '"'.$request->uri().'"';

			if ($request !== \Hanariu\Request::$initial AND Request::$current)
			{
				$benchmark .= ' « "'.\Hanariu\Request::$current->uri().'"';
			}
			$benchmark = \Hanariu\Profiler::start('Requests', $benchmark);
		}

		$previous = \Hanariu\Request::$current;
		\Hanariu\Request::$current = $request;
		if ($post = $request->post())
		{
			$request->body(http_build_query($post, NULL, '&'))
				->headers('content-type', 'application/x-www-form-urlencoded');
		}

		if (\Hanariu\Hanariu::$expose)
		{
			$request->headers('user-agent', \Hanariu\Hanariu::version());
		}

		try
		{
			$response = $this->_send_message($request, $response);
		}
		catch (\Hanariu\Exception $e)
		{
			\Hanariu\Request::$current = $previous;

			if (isset($benchmark))
			{
				\Hanariu\Profiler::delete($benchmark);
			}

			throw $e;
		}

		\Hanariu\Request::$current = $previous;

		if (isset($benchmark))
		{
			\Hanariu\Profiler::stop($benchmark);
		}

		return $response;
	}

	public function options($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_options;

		if (\is_array($key))
		{
			$this->_options = $key;
		}
		elseif ($value === NULL)
		{
			return \Hanariu\Arr::get($this->_options, $key);
		}
		else
		{
			$this->_options[$key] = $value;
		}

		return $this;
	}


	abstract protected function _send_message(\Hanariu\Request $request, \Hanariu\Response $response);

}
