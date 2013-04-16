<?php namespace Hanariu\HTTP\Exception;

class E405 extends Expected {


	protected $_code = 405;


	public function allowed($methods)
	{
		if (is_array($methods))
		{
			$methods = implode(',', $methods);
		}

		$this->headers('allow', $methods);

		return $this;
	}


	public function check()
	{
		if ($location = $this->headers('allow') === NULL)
			throw new \Hanariu\Exception('A list of allowed methods must be specified');

		return TRUE;
	}

}
