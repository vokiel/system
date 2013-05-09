<?php namespace Hanariu\Core\HTTP\Exception;

class Error304 extends Expected {

	/**
	 * @var   integer    HTTP 304 Not Modified
	 */
	protected $_code = 304;
	
}
