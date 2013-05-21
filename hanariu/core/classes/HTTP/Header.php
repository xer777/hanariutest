<?php namespace Hanariu\Core;

class HTTP_Header extends \ArrayObject {

	public static function accept_quality(array $parts)
	{
		$parsed = array();

		$parts_keys = \array_keys($parts);
		foreach ($parts_keys as $key)
		{
			$value = \trim(\str_replace(array("\r", "\n"), '', $parts[$key]));

			$pattern = '~\b(\;\s*+)?q\s*+=\s*+([.0-9]+)~';

			if ( ! \preg_match($pattern, $value, $quality))
			{
				$parsed[$value] = (float) \Header::DEFAULT_QUALITY;
			}
			else
			{
				$quality = $quality[2];

				if ($quality[0] === '.')
				{
					$quality = '0'.$quality;
				}

				// Remove the quality value from the string and apply quality
				$parsed[\trim(\preg_replace($pattern, '', $value, 1), '; ')] = (float) $quality;
			}
		}

		return $parsed;
	}

	public static function parse_accept_header($accepts = NULL)
	{
		$accepts = \explode(',', (string) $accepts);

		if ($accepts === NULL)
			return array('*' => array('*' => (float) \Header::DEFAULT_QUALITY));

		$accepts = \Header::accept_quality($accepts);
		$parsed_accept = array();
		$keys = \array_keys($accepts);
		foreach ($keys as $key)
		{
			$parts = \explode('/', $key, 2);
			if ( ! isset($parts[1]))
				continue;

			$parsed_accept[$parts[0]][$parts[1]] = $accepts[$key];
		}

		return $parsed_accept;
	}

	public static function parse_charset_header($charset = NULL)
	{
		if ($charset === NULL)
		{
			return array('*' => (float) \Header::DEFAULT_QUALITY);
		}

		return \Header::accept_quality(\explode(',', (string) $charset));
	}

	public static function parse_encoding_header($encoding = NULL)
	{
		// Accept everything
		if ($encoding === NULL)
		{
			return array('*' => (float) \Header::DEFAULT_QUALITY);
		}
		elseif ($encoding === '')
		{
			return array('identity' => (float) \Header::DEFAULT_QUALITY);
		}
		else
		{
			return \Header::accept_quality(\explode(',', (string) $encoding));
		}
	}

	public static function parse_language_header($language = NULL)
	{
		if ($language === NULL)
		{
			return array('*' => array('*' => (float) \Header::DEFAULT_QUALITY));
		}

		$language = \Header::accept_quality(\explode(',', (string) $language));

		$parsed_language = array();

		$keys = \array_keys($language);
		foreach ($keys as $key)
		{
			$parts = \explode('-', $key, 2);

			if ( ! isset($parts[1]))
			{
				$parsed_language[$parts[0]]['*'] = $language[$key];
			}
			else
			{
				$parsed_language[$parts[0]][$parts[1]] = $language[$key];
			}
		}

		return $parsed_language;
	}

	public static function create_cache_control(array $cache_control)
	{
		$parts = array();

		foreach ($cache_control as $key => $value)
		{
			$parts[] = (\is_int($key)) ? $value : ($key.'='.$value);
		}

		return \implode(', ', $parts);
	}

	public static function parse_cache_control($cache_control)
	{
		$directives = \explode(',', \strtolower($cache_control));

		if ($directives === FALSE)
			return FALSE;

		$output = array();

		foreach ($directives as $directive)
		{
			if (\strpos($directive, '=') !== FALSE)
			{
				list($key, $value) = \explode('=', \trim($directive), 2);

				$output[$key] = \ctype_digit($value) ? (int) $value : $value;
			}
			else
			{
				$output[] = \trim($directive);
			}
		}

		return $output;
	}

	protected $_accept_content;
	protected $_accept_charset;
	protected $_accept_encoding;
	protected $_accept_language;

	public function __construct(array $input = array(), $flags = NULL, $iterator_class = 'ArrayIterator')
	{
		$input = \array_change_key_case( (array) $input, CASE_LOWER);
		parent::__construct($input, $flags, $iterator_class);
	}

	public function __toString()
	{
		$header = '';

		foreach ($this as $key => $value)
		{
			$key = \Utils::ucfirst($key);

			if (\is_array($value))
			{
				$header .= $key.': '.(\implode(', ', $value))."\r\n";
			}
			else
			{
				$header .= $key.': '.$value."\r\n";
			}
		}

		return $header."\r\n";
	}

	public function offsetSet($index, $newval, $replace = TRUE)
	{
		// Ensure the index is lowercase
		$index = \strtolower($index);

		if ($replace OR ! $this->offsetExists($index))
		{
			return parent::offsetSet($index, $newval);
		}

		$current_value = $this->offsetGet($index);

		if (\is_array($current_value))
		{
			$current_value[] = $newval;
		}
		else
		{
			$current_value = array($current_value, $newval);
		}

		return parent::offsetSet($index, $current_value);
	}

	public function offsetExists($index)
	{
		return parent::offsetExists(\strtolower($index));
	}

	public function offsetUnset($index)
	{
		return parent::offsetUnset(\strtolower($index));
	}

	public function offsetGet($index)
	{
		return parent::offsetGet(\strtolower($index));
	}

	public function exchangeArray($input)
	{
		/**
		 * @link http://www.w3.org/Protocols/rfc2616/rfc2616.html
		 *
		 * HTTP header declarations should be treated as case-insensitive
		 */
		$input = \array_change_key_case( (array) $input, CASE_LOWER);

		return parent::exchangeArray($input);
	}


	public function parse_header_string($resource, $header_line)
	{
		$headers = array();

		if (\preg_match_all('/(\w[^\s:]*):[ ]*([^\r\n]*(?:\r\n[ \t][^\r\n]*)*)/', $header_line, $matches))
		{
			foreach ($matches[0] as $key => $value)
			{
				$this->offsetSet($matches[1][$key], $matches[2][$key], FALSE);
			}
		}

		return \strlen($header_line);
	}

	public function accepts_at_quality($type, $explicit = FALSE)
	{
		// Parse Accept header if required
		if ($this->_accept_content === NULL)
		{
			if ($this->offsetExists('Accept'))
			{
				$accept = $this->offsetGet('Accept');
			}
			else
			{
				$accept = '*/*';
			}

			$this->_accept_content = \Header::parse_accept_header($accept);
		}

		// If not a real mime, try and find it in config
		if (\strpos($type, '/') === FALSE)
		{
			$mime = \Hanariu::$config->load('mimes.'.$type);

			if ($mime === NULL)
				return FALSE;

			$quality = FALSE;

			foreach ($mime as $_type)
			{
				$quality_check = $this->accepts_at_quality($_type, $explicit);
				$quality = ($quality_check > $quality) ? $quality_check : $quality;
			}

			return $quality;
		}

		$parts = \explode('/', $type, 2);

		if (isset($this->_accept_content[$parts[0]][$parts[1]]))
		{
			return $this->_accept_content[$parts[0]][$parts[1]];
		}
		elseif ($explicit === TRUE)
		{
			return FALSE;
		}
		else
		{
			if (isset($this->_accept_content[$parts[0]]['*']))
			{
				return $this->_accept_content[$parts[0]]['*'];
			}
			elseif (isset($this->_accept_content['*']['*']))
			{
				return $this->_accept_content['*']['*'];
			}
			else
			{
				return FALSE;
			}
		}
	}

	public function preferred_accept(array $types, $explicit = FALSE)
	{
		$preferred = FALSE;
		$ceiling = 0;

		foreach ($types as $type)
		{
			$quality = $this->accepts_at_quality($type, $explicit);

			if ($quality > $ceiling)
			{
				$preferred = $type;
				$ceiling = $quality;
			}
		}

		return $preferred;
	}

	public function accepts_charset_at_quality($charset)
	{
		if ($this->_accept_charset === NULL)
		{
			if ($this->offsetExists('Accept-Charset'))
			{
				$charset_header = \strtolower($this->offsetGet('Accept-Charset'));
				$this->_accept_charset = \Header::parse_charset_header($charset_header);
			}
			else
			{
				$this->_accept_charset = \Header::parse_charset_header(NULL);
			}
		}

		$charset = \strtolower($charset);

		if (isset($this->_accept_charset[$charset]))
		{
			return $this->_accept_charset[$charset];
		}
		elseif (isset($this->_accept_charset['*']))
		{
			return $this->_accept_charset['*'];
		}
		elseif ($charset === 'iso-8859-1')
		{
			return (float) 1;
		}

		return (float) 0;
	}

	public function preferred_charset(array $charsets)
	{
		$preferred = FALSE;
		$ceiling = 0;

		foreach ($charsets as $charset)
		{
			$quality = $this->accepts_charset_at_quality($charset);

			if ($quality > $ceiling)
			{
				$preferred = $charset;
				$ceiling = $quality;
			}
		}

		return $preferred;
	}

	public function accepts_encoding_at_quality($encoding, $explicit = FALSE)
	{
		if ($this->_accept_encoding === NULL)
		{
			if ($this->offsetExists('Accept-Encoding'))
			{
				$encoding_header = $this->offsetGet('Accept-Encoding');
			}
			else
			{
				$encoding_header = NULL;
			}

			$this->_accept_encoding = \HTTP_Header::parse_encoding_header($encoding_header);
		}

		// Normalize the encoding
		$encoding = \strtolower($encoding);

		if (isset($this->_accept_encoding[$encoding]))
		{
			return $this->_accept_encoding[$encoding];
		}

		if ($explicit === FALSE)
		{
			if (isset($this->_accept_encoding['*']))
			{
				return $this->_accept_encoding['*'];
			}
			elseif ($encoding === 'identity')
			{
				return (float) \Header::DEFAULT_QUALITY;
			}
		}

		return (float) 0;
	}

	public function preferred_encoding(array $encodings, $explicit = FALSE)
	{
		$ceiling = 0;
		$preferred = FALSE;

		foreach ($encodings as $encoding)
		{
			$quality = $this->accepts_encoding_at_quality($encoding, $explicit);

			if ($quality > $ceiling)
			{
				$ceiling = $quality;
				$preferred = $encoding;
			}
		}

		return $preferred;
	}

	public function accepts_language_at_quality($language, $explicit = FALSE)
	{
		if ($this->_accept_language === NULL)
		{
			if ($this->offsetExists('Accept-Language'))
			{
				$language_header = \strtolower($this->offsetGet('Accept-Language'));
			}
			else
			{
				$language_header = NULL;
			}

			$this->_accept_language = \Header::parse_language_header($language_header);
		}

		$language_parts = \explode('-', \strtolower($language), 2);

		if (isset($this->_accept_language[$language_parts[0]]))
		{
			if (isset($language_parts[1]))
			{
				if (isset($this->_accept_language[$language_parts[0]][$language_parts[1]]))
				{
					return $this->_accept_language[$language_parts[0]][$language_parts[1]];
				}
				elseif ($explicit === FALSE AND isset($this->_accept_language[$language_parts[0]]['*']))
				{
					return $this->_accept_language[$language_parts[0]]['*'];
				}
			}
			elseif (isset($this->_accept_language[$language_parts[0]]['*']))
			{
				return $this->_accept_language[$language_parts[0]]['*'];
			}
		}

		if ($explicit === FALSE AND isset($this->_accept_language['*']))
		{
			return $this->_accept_language['*'];
		}

		return (float) 0;
	}

	public function preferred_language(array $languages, $explicit = FALSE)
	{
		$ceiling = 0;
		$preferred = FALSE;

		foreach ($languages as $language)
		{
			$quality = $this->accepts_language_at_quality($language, $explicit);

			if ($quality > $ceiling)
			{
				$ceiling = $quality;
				$preferred = $language;
			}
		}

		return $preferred;
	}


	public function send_headers(\Response $response = NULL, $replace = FALSE, $callback = NULL)
	{
		if ($response === NULL)
		{
			$response = \Request::initial()->response();
		}

		$protocol = $response->protocol();
		$status = $response->status();
		$processed_headers = array($protocol.' '.$status.' '.\Response::$messages[$status]);
		$headers = $response->headers()->getArrayCopy();

		foreach ($headers as $header => $value)
		{
			if (\is_array($value))
			{
				$value = implode(', ', $value);
			}

			$processed_headers[] = \Utils::ucfirst($header).': '.$value;
		}

		if ( ! isset($headers['content-type']))
		{
			$processed_headers[] = 'Content-Type: '.\Hanariu::$content_type.'; charset='.\Hanariu::$charset;
		}

		if (\Hanariu::$expose AND ! isset($headers['x-powered-by']))
		{
			$processed_headers[] = 'X-Powered-By: '.\Hanariu::version();
		}

		if ($cookies = $response->cookie())
		{
			$processed_headers['Set-Cookie'] = $cookies;
		}

		if (\is_callable($callback))
		{
			return \call_user_func($callback, $response, $processed_headers, $replace);
		}
		else
		{
			$this->_send_headers_to_php($processed_headers, $replace);
			return $response;
		}
	}

	protected function _send_headers_to_php(array $headers, $replace)
	{
		if (\headers_sent())
			return $this;

		foreach ($headers as $key => $line)
		{
			if ($key == 'Set-Cookie' AND is_array($line))
			{
				// Send cookies
				foreach ($line as $name => $value)
				{
					\Cookie::set($name, $value['value'], $value['expiration']);
				}

				continue;
			}

			\header($line, $replace);
		}

		return $this;
	}

}
