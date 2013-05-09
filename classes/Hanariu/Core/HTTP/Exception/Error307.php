<?php namespace Hanariu\Core\HTTP\Exception;

class Error307 extends Redirect {

	/**
	 * @var   integer    HTTP 307 Temporary Redirect
	 */
	protected $_code = 307;

}
