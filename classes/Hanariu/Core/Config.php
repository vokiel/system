<?php namespace Hanariu\Core;

class Config {

	protected $_sources = array();

	public function load($group)
	{

		if ($files = Hanariu::find_file('config', $group, NULL, TRUE))
		{
			foreach ($files as $file)
			{
				$this->_sources = \Hanariu\Arr::merge($this->_sources, \Hanariu::load($file));
			}
		}

		if( ! \count($this->_sources))
		{
			\Hanariu\Exception::handler('No configuration sources attached');
		}

		if (empty($group))
		{
			\Hanariu\Exception::handler("Need to specify a config group");
		}

		if ( ! \is_string($group))
		{
			\Hanariu\Exception::handler("Config group must be a string");
		}

		// We search from the "lowest" source and work our way up
		$sources = \array_reverse($this->_sources);

		$this->_groups[$group] = new \Hanariu\Config\Group($this, $group, $sources);

		if (isset($path))
		{
			return \Hanariu\Arr::path($config, $path, NULL, '.');
		}

		$this->_sources = array();
		return $this->_groups[$group];
	}

}
