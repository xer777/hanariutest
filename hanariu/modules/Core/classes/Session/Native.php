<?php namespace Core;

class Session_Native extends \Core\Session {

	public function id()
	{
		return \session_id();
	}

	protected function _read($id = NULL)
	{
		\session_set_cookie_params($this->_lifetime, \Cookie::$path, \Cookie::$domain, \Cookie::$secure, \Cookie::$httponly);
		\session_cache_limiter(FALSE);
		\session_name($this->_name);

		if ($id)
		{
			\session_id($id);
		}

		\session_start();
		$this->_data =& $_SESSION;

		return NULL;
	}

	protected function _regenerate()
	{
		\session_regenerate_id();
		return \session_id();
	}

	protected function _write()
	{
		\session_write_close();
		return TRUE;
	}

	protected function _restart()
	{
		$status = \session_start();
		$this->_data =& $_SESSION;
		return $status;
	}

	protected function _destroy()
	{
		\session_destroy();
		$status = ! \session_id();

		if ($status)
		{
			\Cookie::delete($this->_name);
		}

		return $status;
	}

}
