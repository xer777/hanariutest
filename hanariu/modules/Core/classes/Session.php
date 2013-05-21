<?php namespace Core;

abstract class Session {

	public static $default = 'native';
	public static $instances = array();

	public static function instance($type = NULL, $id = NULL)
	{
		if ($type === NULL)
		{
			$type = \Core\Session::$default;
		}

		if ( ! isset(\Core\Session::$instances[$type]))
		{
			$config = \Core\Hanariu::$config->load('session')->get($type);
			$class = '\\Core\\Session\\'.\ucfirst($type);
			\Core\Session::$instances[$type] = $session = new $class($config, $id);
			\register_shutdown_function(array($session, 'write'));
		}

		return \Core\Session::$instances[$type];
	}

	protected $_name = 'session';
	protected $_lifetime = 0;
	protected $_encrypted = FALSE;
	protected $_data = array();
	protected $_destroyed = FALSE;


	public function __construct(array $config = NULL, $id = NULL)
	{
		if (isset($config['name']))
		{
			$this->_name = (string) $config['name'];
		}

		if (isset($config['lifetime']))
		{
			$this->_lifetime = (int) $config['lifetime'];
		}

		if (isset($config['encrypted']))
		{
			if ($config['encrypted'] === TRUE)
			{
				// Use the default Encrypt instance
				$config['encrypted'] = 'default';
			}

			$this->_encrypted = $config['encrypted'];
		}

		$this->read($id);
	}

	public function __toString()
	{
		$data = $this->_serialize($this->_data);

		if ($this->_encrypted)
		{
			$data = \Core\Encrypt::instance($this->_encrypted)->encode($data);
		}
		else
		{
			$data = $this->_encode($data);
		}

		return $data;
	}


	public function & as_array()
	{
		return $this->_data;
	}

	public function id()
	{
		return NULL;
	}

	public function name()
	{
		return $this->_name;
	}


	public function get($key, $default = NULL)
	{
		return \array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
	}


	public function get_once($key, $default = NULL)
	{
		$value = $this->get($key, $default);

		unset($this->_data[$key]);

		return $value;
	}


	public function set($key, $value)
	{
		$this->_data[$key] = $value;

		return $this;
	}


	public function bind($key, & $value)
	{
		$this->_data[$key] =& $value;

		return $this;
	}


	public function delete($key)
	{
		$args = \func_get_args();

		foreach ($args as $key)
		{
			unset($this->_data[$key]);
		}

		return $this;
	}


	public function read($id = NULL)
	{
		$data = NULL;

		try
		{
			if (\is_string($data = $this->_read($id)))
			{
				if ($this->_encrypted)
				{
					$data = \Core\Encrypt::instance($this->_encrypted)->decode($data);
				}
				else
				{
					$data = $this->_decode($data);
				}

				$data = $this->_unserialize($data);
			}

		}
		catch (Exception $e)
		{
			throw new \Core\Session\Exception('Error reading session data.', NULL, \Core\Session\Exception::SESSION_CORRUPT);
		}

		if (\is_array($data))
		{
			$this->_data = $data;
		}
	}

	public function regenerate()
	{
		return $this->_regenerate();
	}

	public function write()
	{
		if (\headers_sent() OR $this->_destroyed)
		{
			return FALSE;
		}

		$this->_data['last_active'] = \time();

		try
		{
			return $this->_write();
		}
		catch (\Core_Exception $e)
		{
			\Hanariu::$log->add(\Log::ERROR, \Core_Exception::text($e))->write();

			return FALSE;
		}
	}

	public function destroy()
	{
		if ($this->_destroyed === FALSE)
		{
			if ($this->_destroyed = $this->_destroy())
			{
				$this->_data = array();
			}
		}

		return $this->_destroyed;
	}

	public function restart()
	{
		if ($this->_destroyed === FALSE)
		{
			$this->destroy();
		}

		$this->_destroyed = FALSE;
		return $this->_restart();
	}

	protected function _serialize($data)
	{
		return \serialize($data);
	}

	protected function _unserialize($data)
	{
		return \unserialize($data);
	}

	protected function _encode($data)
	{
		return \base64_encode($data);
	}

	protected function _decode($data)
	{
		return \base64_decode($data);
	}

	abstract protected function _read($id = NULL);
	abstract protected function _regenerate();
	abstract protected function _write();
	abstract protected function _destroy();
	abstract protected function _restart();

}
