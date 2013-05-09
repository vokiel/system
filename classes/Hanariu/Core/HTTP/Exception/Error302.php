<?php namespace Hanariu\Core\HTTP\Exception;

class Error302 extends Redirect {

	/**
	 * @var   integer    HTTP 302 Found
	 */
	protected $_code = 302;

}
