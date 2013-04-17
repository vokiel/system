<?php namespace Hanariu\Request\Client;

class Internal extends \Hanariu\Request\Client {

	protected $_previous_environment;

	public function execute_request(\Hanariu\Request $request, \Hanariu\Response $response)
	{
		$prefix = 'Controller\\';
		$directory = $request->directory();
		$controller = $request->controller();

		if ($directory)
		{
			$prefix .= str_replace(array('\\', '/'), '_', trim($directory, '/')).'\\';
		}

		if (\Hanariu\Hanariu::$profiling)
		{
			$benchmark = '"'.$request->uri().'"';

			if ($request !== \Hanariu\Request::$initial AND \Hanariu\Request::$current)
			{
				$benchmark .= ' « "'.\Hanariu\Request::$current->uri().'"';
			}
			$benchmark = \Hanariu\Profiler::start('Requests', $benchmark);
		}

		$previous = \Hanariu\Request::$current;
		\Hanariu\Request::$current = $request;
		$initial_request = ($request === \Hanariu\Request::$initial);

		try
		{
			if ( ! class_exists($prefix.$controller))
			{
				throw \Hanariu\HTTP\Exception::factory(404,
					'The requested URL :uri  where prefix and controller are  :pc was not found on this server.',
					array(':uri' => $request->uri(),':pc' => $prefix.$controller)
				)->request($request);
			}

			$class = new \ReflectionClass($prefix.$controller);

			if ($class->isAbstract())
			{
				throw new \Hanariu\Exception(
					'Cannot create instances of abstract :controller',
					array(':controller' => $prefix.$controller)
				);
			}

			$controller = $class->newInstance($request, $response);
			$response = $class->getMethod('execute')->invoke($controller);

			if ( ! $response instanceof \Hanariu\Response)
			{
				throw new \Hanariu\Exception('Controller failed to return a Response');
			}
		}
		catch (\Hanariu\HTTP\Exception $e)
		{
			$response = $e->get_response();
		}
		catch (\Exception $e)
		{
			$response = \Hanariu\Exception::_handler($e);
		}

		\Hanariu\Request::$current = $previous;

		if (isset($benchmark))
		{
			\Hanariu\Profiler::stop($benchmark);
		}

		return $response;
	}
}
