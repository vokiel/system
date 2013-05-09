<?php namespace Hanariu\Core\HTTP\Exception;

class Error408 extends \Hanariu\HTTP\Exception {

	/**
	 * @var   integer    HTTP 408 Request Timeout
	 */
	protected $_code = 408;

}
