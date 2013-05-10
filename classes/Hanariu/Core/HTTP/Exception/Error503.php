<?php namespace Hanariu\Core\HTTP\Exception;

class Error503 extends \Hanariu\HTTP\Exception {

	/**
	 * @var   integer    HTTP 503 Service Unavailable
	 */
	protected $_code = 503;

}
