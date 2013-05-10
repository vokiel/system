<?php namespace Hanariu\Core\HTTP;

abstract class Exception extends \Hanariu\Exception {

	public static function factory($code, $message = NULL, array $variables = NULL, \Hanariu\Exception $previous = NULL)
	{

		$class = '\\Hanariu\\HTTP\\Exception\\Error'.$code;
		return new $class($message, $variables, $previous);
		
	}

	protected $_code = 0;
	protected $_request;

	public function __construct($message = NULL, array $variables = NULL, \Hanariu\Exception $previous = NULL)
	{
		parent::__construct($message, $variables, $this->_code, $previous);
	}

	public function request(\Hanariu\Request $request = NULL)
	{
		if ($request === NULL)
			return $this->_request;
		
		$this->_request = $request;

		return $this;
	}

	public function get_response()
	{
		return \Hanariu\Exception::response($this);
	}

}
