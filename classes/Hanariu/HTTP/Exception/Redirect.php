<?php namespace Hanariu\HTTP\Exception;

abstract class Redirect extends Expected {

	public function location($uri = NULL)
	{
		if ($uri === NULL)
			return $this->headers('Location');
		
		if (\strpos($uri, '://') === FALSE)
		{
			$uri = \Hanariu\URL::site($uri, TRUE, ! empty(\Hanariu\Hanariu::$index_file));
		}

		$this->headers('Location', $uri);

		return $this;
	}

	public function check()
	{
		if ($this->headers('location') === NULL)
			throw new \Hanariu\Exception('A \'location\' must be specified for a redirect');

		return TRUE;
	}

}
