<?php namespace Hanariu\Core;

class Request_Client_Stream extends Request_Client_External {

	public function _send_message(Request $request, Response $response)
	{
		$mode = ($request->method() === HTTP_Request::GET) ? 'r' : 'r+';

		if ($cookies = $request->cookie())
		{
			$request->headers('cookie', \http_build_query($cookies, NULL, '; '));
		}

		$body = $request->body();

		if (\is_resource($body))
		{
			$body = \stream_get_contents($body);
		}

		$request->headers('content-length', (string) \strlen($body));

		list($protocol) = \explode('/', $request->protocol());

		$options = array(
			\strtolower($protocol) => array(
				'method'     => $request->method(),
				'header'     => (string) $request->headers(),
				'content'    => $body
			)
		);

		$context = \stream_context_create($options);

		\stream_context_set_option($context, $this->_options);

		$uri = $request->uri();

		if ($query = $request->query())
		{
			$uri .= '?'.\http_build_query($query, NULL, '&');
		}

		$stream = \fopen($uri, $mode, FALSE, $context);
		$meta_data = \stream_get_meta_data($stream);
		$http_response = \array_shift($meta_data['wrapper_data']);

		if (\preg_match_all('/(\w+\/\d\.\d) (\d{3})/', $http_response, $matches) !== FALSE)
		{
			$protocol = $matches[1][0];
			$status   = (int) $matches[2][0];
		}
		else
		{
			$protocol = NULL;
			$status   = NULL;
		}

		$response_header = $response->headers();
		\array_map(array($response_header, 'parse_header_string'), array(), $meta_data['wrapper_data']);

		$response->status($status)
			->protocol($protocol)
			->body(\stream_get_contents($stream));

		\fclose($stream);

		return $response;
	}

}
