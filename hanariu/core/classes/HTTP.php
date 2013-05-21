<?php namespace Hanariu\Core;

abstract class HTTP {


	public static $protocol = 'HTTP/1.1';

	public static function redirect($uri = '', $code = 302)
	{
		$e = \HTTP_Exception::factory($code);

		if ( ! $e instanceof \HTTP_Exception_Redirect)
			throw new \Core_Exception('Invalid redirect code \':code\'', array(
				':code' => $code
			));

		throw $e->location($uri);
	}

	public static function check_cache(\Request $request, \Response $response, $etag = NULL)
	{
		if ($etag == NULL)
		{
			$etag = $response->generate_etag();
		}

		$response->headers('etag', $etag);

		if ($response->headers('cache-control'))
		{
			$response->headers('cache-control', $response->headers('cache-control').', must-revalidate');
		}
		else
		{
			$response->headers('cache-control', 'must-revalidate');
		}

		if ($request->headers('if-none-match') AND (string) $request->headers('if-none-match') === $etag)
		{
			throw \HTTP_Exception::factory(304)->headers('etag', $etag);
		}

		return $response;
	}


	public static function parse_header_string($header_string)
	{
		if (\extension_loaded('http'))
		{
			return new \HTTP_Header(\http_parse_headers($header_string));
		}

		$headers = array();

		if (\preg_match_all('/(\w[^\s:]*):[ ]*([^\r\n]*(?:\r\n[ \t][^\r\n]*)*)/', $header_string, $matches))
		{
			foreach ($matches[0] as $key => $value)
			{
				if ( ! isset($headers[$matches[1][$key]]))
				{
					$headers[$matches[1][$key]] = $matches[2][$key];
				}
				else
				{
					if (\is_array($headers[$matches[1][$key]]))
					{
						$headers[$matches[1][$key]][] = $matches[2][$key];
					}
					else
					{
						$headers[$matches[1][$key]] = array(
							$headers[$matches[1][$key]],
							$matches[2][$key],
						);
					}
				}
			}
		}

		return new \HTTP_Header($headers);
	}

	public static function request_headers()
	{
		if (\function_exists('apache_request_headers'))
		{
			return new \HTTP_Header(\apache_request_headers());
		}
		elseif (\extension_loaded('http'))
		{
			return new \HTTP_Header(\http_get_request_headers());
		}

		$headers = array();
		if ( ! empty($_SERVER['CONTENT_TYPE']))
		{
			$headers['content-type'] = $_SERVER['CONTENT_TYPE'];
		}

		if ( ! empty($_SERVER['CONTENT_LENGTH']))
		{
			$headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
		}

		foreach ($_SERVER as $key => $value)
		{
			if (\strpos($key, 'HTTP_') !== 0)
			{
				continue;
			}

			$headers[\str_replace(array('HTTP_', '_'), array('', '-'), $key)] = $value;
		}

		return new \HTTP_Header($headers);
	}


	public static function www_form_urlencode(array $params = array())
	{
		if ( ! $params)
			return;

		$encoded = array();

		foreach ($params as $key => $value)
		{
			$encoded[] = $key.'='.\rawurlencode($value);
		}

		return \implode('&', $encoded);
	}
}
