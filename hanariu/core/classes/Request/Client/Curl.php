<?php namespace Hanariu\Core;

class Request_Client_Curl extends Request_Client_External {

	public function _send_message(\Request $request, \Response $response)
	{
		$response_headers = array();
		$options = array();
		$options = $this->_set_curl_request_method($request, $options);
		$options[CURLOPT_POSTFIELDS] = $request->body();

		if ($headers = $request->headers())
		{
			$http_headers = array();

			foreach ($headers as $key => $value)
			{
				$http_headers[] = $key.': '.$value;
			}

			$options[CURLOPT_HTTPHEADER] = $http_headers;
		}

		if ($cookies = $request->cookie())
		{
			$options[CURLOPT_COOKIE] = \http_build_query($cookies, NULL, '; ');
		}

		$response_header = $response->headers();
		$options[CURLOPT_HEADERFUNCTION]        = array($response_header, 'parse_header_string');
		$this->_options[CURLOPT_RETURNTRANSFER] = TRUE;
		$this->_options[CURLOPT_HEADER]         = FALSE;
		$options += $this->_options;

		$uri = $request->uri();

		if ($query = $request->query())
		{
			$uri .= '?'.\http_build_query($query, NULL, '&');
		}

		$curl = \curl_init($uri);

		if ( ! \curl_setopt_array($curl, $options))
		{
			throw new \Core_Exception('Failed to set CURL options, check CURL documentation: :url',
				array(':url' => 'http://php.net/curl_setopt_array'));
		}

		$body = \curl_exec($curl);
		$code = \curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($body === FALSE)
		{
			$error = \curl_error($curl);
		}

		\curl_close($curl);

		if (isset($error))
		{
			throw new \Core_Exception('Error fetching remote :url [ status :code ] :error',
				array(':url' => $request->url(), ':code' => $code, ':error' => $error));
		}

		$response->status($code)
			->body($body);

		return $response;
	}

	public function _set_curl_request_method(\Request $request, array $options)
	{
		switch ($request->method()) {
			case \Request::POST:
				$options[CURLOPT_POST] = TRUE;
				break;
			case \Request::PUT:
				$options[CURLOPT_PUT] = TRUE;
				break;
			default:
				$options[CURLOPT_CUSTOMREQUEST] = $request->method();
				break;
		}
		return $options;
	}

}
