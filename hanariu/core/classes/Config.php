<?php namespace Hanariu\Core;

class Config {

	protected $_sources = array();

	public function load($group)
	{
		if ($files = \Hanariu::find_file('config', $group, NULL, TRUE))
		//var_dump(\Finder::search('config', $group, NULL, TRUE));
		//if ($files = \Finder::search('config', $group, NULL, TRUE))
		{
			foreach ($files as $file)
			{
				$this->_sources = \Arr::merge($this->_sources, \Hanariu::load($file));
			}
		}

		if( ! \count($this->_sources))
		{
			\Core_Exception::handler('No configuration sources attached');
		}

		if (empty($group))
		{
			\Core_Exception::handler("Need to specify a config group");
		}

		if ( ! \is_string($group))
		{
			\Core_Exception::handler("Config group must be a string");
		}

		// We search from the "lowest" source and work our way up
		$sources = \array_reverse($this->_sources);

		$this->_groups[$group] = new \Config_Group($this, $group, $sources);

		if (isset($path))
		{
			return \Arr::path($config, $path, NULL, '.');
		}

		$this->_sources = array();
		return $this->_groups[$group];
	}
}
