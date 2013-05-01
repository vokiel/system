<?php namespace Hanariu\Validation;

class Exception extends \Hanariu\Exception {

	public $array;

	public function __construct(\Hanariu\Validation $array, $message = 'Failed to validate array', array $values = NULL, $code = 0, \Hanariu\Exception $previous = NULL)
	{
		$this->array = $array;

		parent::__construct($message, $values, $code, $previous);
	}

} 
