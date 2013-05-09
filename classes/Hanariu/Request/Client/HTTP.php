<?php namespace Hanariu\Request\Client;

class HTTP extends External {

	public function __construct(array $params = array())
	{
		if ( ! http_support(HTTP_SUPPORT_REQUESTS))
		{
			throw new \Hanariu\Exception('Need HTTP request support!');
		}

		parent::__construct($params);
	}


	protected $_options = array();

	public function _send_message(\Hanariu\Request $request, \Hanariu\Response $response)
	{
		$http_method_mapping = array(
			\Hanariu\HTTP\Request::GET     => \HTTPRequest::METH_GET,
			\Hanariu\HTTP\Request::HEAD    => \HTTPRequest::METH_HEAD,
			\Hanariu\HTTP\Request::POST    => \HTTPRequest::METH_POST,
			\Hanariu\HTTP\Request::PUT     => \HTTPRequest::METH_PUT,
			\Hanariu\HTTP\Request::DELETE  => \HTTPRequest::METH_DELETE,
			\Hanariu\HTTP\Request::OPTIONS => \HTTPRequest::METH_OPTIONS,
			\Hanariu\HTTP\Request::TRACE   => \HTTPRequest::METH_TRACE,
			\Hanariu\HTTP\Request::CONNECT => \HTTPRequest::METH_CONNECT,
		);

		$http_request = new \HTTPRequest($request->uri(), $http_method_mapping[$request->method()]);

		if ($this->_options)
		{
			$http_request->setOptions($this->_options);
		}

		$http_request->setHeaders($request->headers()->getArrayCopy());
		$http_request->setCookies($request->cookie());
		$http_request->setQueryData($request->query());
		if ($request->method() == \Hanariu\HTTP\Request::PUT)
		{
			$http_request->addPutData($request->body());
		}
		else
		{
			$http_request->setBody($request->body());
		}

		try
		{
			$http_request->send();
		}
		catch (\HTTPRequestException $e)
		{
			throw new \Hanariu\Exception($e->getMessage());
		}
		catch (\HTTPMalformedHeaderException $e)
		{
			throw new \Hanariu\Exception($e->getMessage());
		}
		catch (\HTTPEncodingException $e)
		{
			throw new \Hanariu\Exception($e->getMessage());
		}

		$response->status($http_request->getResponseCode())
			->headers($http_request->getResponseHeader())
			->cookie($http_request->getResponseCookies())
			->body($http_request->getResponseBody());

		return $response;
	}

}
