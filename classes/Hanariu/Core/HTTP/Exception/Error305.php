<?php namespace Hanariu\Core\HTTP\Exception;

class Error305 extends \Hanariu\HTTP\ExceptionExpected {

	/**
	 * @var   integer    HTTP 305 Use Proxy
	 */
	protected $_code = 305;

	/**
	 * Specifies the proxy to replay this request via
	 * 
	 * @param  string  $location  URI of the proxy
	 */
	public function location($uri = NULL)
	{
		if ($uri === NULL)
			return $this->headers('Location');

		$this->headers('Location', $uri);

		return $this;
	}

	/**
	 * Validate this exception contains everything needed to continue.
	 * 
	 * @throws Hanariu_Exception
	 * @return bool
	 */
	public function check()
	{
		if ($location = $this->headers('location') === NULL)
			throw new \Hanariu\Exception('A \'location\' must be specified for a redirect');

		if (strpos($location, '://') === FALSE)
			throw new \Hanariu\Exception('An absolute URI to the proxy server must be specified');

		return TRUE;
	}
}
