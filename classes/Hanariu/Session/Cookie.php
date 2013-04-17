<?php namespace Hanariu\Session;

class Cookie extends \Hanariu\Session {

	protected function _read($id = NULL)
	{
		return Cookie::get($this->_name, NULL);
	}

	protected function _regenerate()
	{
		return NULL;
	}

	protected function _write()
	{
		return Cookie::set($this->_name, $this->__toString(), $this->_lifetime);
	}

	protected function _restart()
	{
		return TRUE;
	}

	protected function _destroy()
	{
		return Cookie::delete($this->_name);
	}

}
