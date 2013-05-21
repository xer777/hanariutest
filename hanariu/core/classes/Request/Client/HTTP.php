<?php namespace Hanariu\Core;

class Request_Client_HTTP extends Request_Client_External {

	public function __construct(array $params = array())
	{
		if ( ! \http_support(HTTP_SUPPORT_REQUESTS))
		{
			throw new \Core_Exception('Need HTTP request support!');
		}

		parent::__construct($params);
	}


	protected $_options = array();

	public function _send_message(\Request $request, \Response $response)
	{
		$http_method_mapping = array(
			\HTTP_Request::GET     => \HTTPRequest::METH_GET,
			\HTTP_Request::HEAD    => \HTTPRequest::METH_HEAD,
			\HTTP_Request::POST    => \HTTPRequest::METH_POST,
			\HTTP_Request::PUT     => \HTTPRequest::METH_PUT,
			\HTTP_Request::DELETE  => \HTTPRequest::METH_DELETE,
			\HTTP_Request::OPTIONS => \HTTPRequest::METH_OPTIONS,
			\HTTP_Request::TRACE   => \HTTPRequest::METH_TRACE,
			\HTTP_Request::CONNECT => \HTTPRequest::METH_CONNECT,
		);

		$http_request = new \HTTPRequest($request->uri(), $http_method_mapping[$request->method()]);

		if ($this->_options)
		{
			$http_request->setOptions($this->_options);
		}

		$http_request->setHeaders($request->headers()->getArrayCopy());
		$http_request->setCookies($request->cookie());
		$http_request->setQueryData($request->query());
		if ($request->method() == \HTTP_Request::PUT)
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
			throw new \Core_Exception($e->getMessage());
		}
		catch (\HTTPMalformedHeaderException $e)
		{
			throw new \Core_Exception($e->getMessage());
		}
		catch (\HTTPEncodingException $e)
		{
			throw new \Core_Exception($e->getMessage());
		}

		$response->status($http_request->getResponseCode())
			->headers($http_request->getResponseHeader())
			->cookie($http_request->getResponseCookies())
			->body($http_request->getResponseBody());

		return $response;
	}

}
