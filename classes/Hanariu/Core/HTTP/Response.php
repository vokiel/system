<?php namespace Hanariu\Core\HTTP;

interface Response extends \Hanariu\HTTP\Message {

	public function status($code = NULL);

}
