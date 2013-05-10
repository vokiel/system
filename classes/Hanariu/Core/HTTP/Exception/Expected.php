<?php namespace Hanariu\Core\HTTP\Exception;

abstract class Expected extends \Hanariu\HTTP\Exception {

	protected $_response;

	public function __construct($message = NULL, array $variables = NULL, \Hanariu\Exception $previous = NULL)
	{
		parent::__construct($message, $variables, $previous);

		$this->_response = \Hanariu\Response::factory()
			->status($this->_code);
	}

	public function headers($key = NULL, $value = NULL)
	{
		$result = $this->_response->headers($key, $value);

		if ( ! $result instanceof \Hanariu\Response)
			return $result;

		return $this;
	}

	public function check()
	{
		return TRUE;
	}

	public function get_response()
	{
		$this->check();

		return $this->_response;
	}

}
