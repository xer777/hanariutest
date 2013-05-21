<?php namespace Hanariu\Core;

class Response implements \HTTP_Response {


	public static function factory(array $config = array())
	{
		return new \Response($config);
	}

	public static $messages = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',

		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',

		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		// 306 is deprecated but reserved
		307 => 'Temporary Redirect',

		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',

		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded'
	);

	protected $_status = 200;
	protected $_header;
	protected $_body = '';
	protected $_cookies = array();
	protected $_protocol;

	public function __construct(array $config = array())
	{
		$this->_header = new \HTTP_Header;

		foreach ($config as $key => $value)
		{
			if (\property_exists($this, $key))
			{
				if ($key == '_header')
				{
					$this->headers($value);
				}
				else
				{
					$this->$key = $value;
				}
			}
		}
	}

	public function __toString()
	{
		return $this->_body;
	}

	public function body($content = NULL)
	{
		if ($content === NULL)
			return $this->_body;

		$this->_body = (string) $content;
		return $this;
	}

	public function protocol($protocol = NULL)
	{
		if ($protocol)
		{
			$this->_protocol = \strtoupper($protocol);
			return $this;
		}

		if ($this->_protocol === NULL)
		{
			$this->_protocol = \HTTP::$protocol;
		}

		return $this->_protocol;
	}


	public function status($status = NULL)
	{
		if ($status === NULL)
		{
			return $this->_status;
		}
		elseif (\array_key_exists($status, \Response::$messages))
		{
			$this->_status = (int) $status;
			return $this;
		}
		else
		{
			throw new \Core_Exception(__METHOD__.' unknown status value : :value', array(':value' => $status));
		}
	}

	public function headers($key = NULL, $value = NULL)
	{
		if ($key === NULL)
		{
			return $this->_header;
		}
		elseif (\is_array($key))
		{
			$this->_header->exchangeArray($key);
			return $this;
		}
		elseif ($value === NULL)
		{
			return \Arr::get($this->_header, $key);
		}
		else
		{
			$this->_header[$key] = $value;
			return $this;
		}
	}

	public function content_length()
	{
		return \strlen($this->body());
	}

	public function cookie($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_cookies;
		elseif ( ! \is_array($key) AND ! $value)
			return \Arr::get($this->_cookies, $key);

		if (is_array($key))
		{
			\reset($key);
			while (list($_key, $_value) = \each($key))
			{
				$this->cookie($_key, $_value);
			}
		}
		else
		{
			if ( ! \is_array($value))
			{
				$value = array(
					'value' => $value,
					'expiration' => \Cookie::$expiration
				);
			}
			elseif ( ! isset($value['expiration']))
			{
				$value['expiration'] = \Cookie::$expiration;
			}

			$this->_cookies[$key] = $value;
		}

		return $this;
	}

	public function delete_cookie($name)
	{
		unset($this->_cookies[$name]);
		return $this;
	}

	public function delete_cookies()
	{
		$this->_cookies = array();
		return $this;
	}


	public function send_headers($replace = FALSE, $callback = NULL)
	{
		return $this->_header->send_headers($this, $replace, $callback);
	}

	public function render()
	{
		if ( ! $this->_header->offsetExists('content-type'))
		{
			$this->_header['content-type'] = \Hanariu::$content_type.'; charset='.\Hanariu::$charset;
		}

		$this->headers('content-length', (string) $this->content_length());

		if (\Hanariu::$expose)
		{
			$this->headers('user-agent', \Hanariu::version());
		}

		if ($this->_cookies)
		{
			if (\extension_loaded('http'))
			{
				$this->_header['set-cookie'] = \http_build_cookie($this->_cookies);
			}
			else
			{
				$cookies = array();
				foreach ($this->_cookies as $key => $value)
				{
					$string = $key.'='.$value['value'].'; expires='.\date('l, d M Y H:i:s T', $value['expiration']);
					$cookies[] = $string;
				}

				$this->_header['set-cookie'] = $cookies;
			}
		}

		$output = $this->_protocol.' '.$this->_status.' '.\Response::$messages[$this->_status]."\r\n";
		$output .= (string) $this->_header;
		$output .= $this->_body;

		return $output;
	}


	public function generate_etag()
	{
	    if ($this->_body === '')
		{
			throw new \Core_Exception('No response yet associated with request - cannot auto generate resource ETag');
		}

		return '"'.\sha1($this->render()).'"';
	}

	protected function _parse_byte_range()
	{
		if ( ! isset($_SERVER['HTTP_RANGE']))
		{
			return FALSE;
		}

		\preg_match_all('/(-?[0-9]++(?:-(?![0-9]++))?)(?:-?([0-9]++))?/', $_SERVER['HTTP_RANGE'], $matches, PREG_SET_ORDER);

		return $matches[0];
	}

	protected function _calculate_byte_range($size)
	{
		$start = 0;
		$end = $size - 1;

		if ($range = $this->_parse_byte_range())
		{
			$start = $range[1];

			if ($start[0] === '-')
			{
				$start = $size - abs($start);
			}

			if (isset($range[2]))
			{
				// Set the end range
				$end = $range[2];
			}
		}

		$start = \abs(\intval($start));
		$end = \min(\abs(\intval($end)), $size - 1);
		$start = ($end < $start) ? 0 : \max($start, 0);

		return array($start, $end);
	}
}
