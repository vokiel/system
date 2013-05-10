<?php namespace Hanariu\Core\HTTP\Exception;

class Error301 extends Redirect {

	/**
	 * @var   integer    HTTP 301 Moved Permanently
	 */
	protected $_code = 301;

}
