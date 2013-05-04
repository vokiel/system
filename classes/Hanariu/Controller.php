<?php namespace Hanariu;

abstract class Controller {

	public $request;
	public $response;

	public function __construct(\Hanariu\Request $request, \Hanariu\Response $response)
	{
		$this->request = $request;
		$this->response = $response;
	}


	public function execute()
	{
		$this->before();
		$action = 'action_'.$this->request->action();

		if ( ! \method_exists($this, $action))
		{
			throw \Hanariu\HTTP\Exception::factory(404,
				'The requested URL :uri was not found on this server.',
				array(':uri' => $this->request->uri())
			)->request($this->request);
		}

		$this->{$action}();
		$this->after();
		return $this->response;
	}


	public function before(){}
	
	public function after(){}


	public static function redirect($uri = '', $code = 302)
	{
		return \Hanariu\HTTP::redirect($uri, $code);
	}

	protected function check_cache($etag = NULL)
	{
		return \Hanariu\HTTP::check_cache($this->request, $this->response, $etag);
	}

}
