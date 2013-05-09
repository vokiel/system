<?php namespace Hanariu\HTTP;

interface Response extends Message {

	public function status($code = NULL);

}
