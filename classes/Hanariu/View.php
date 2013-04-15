<?php namespace Hanariu;

class View {

	protected static $_global_data = array();

	public static function factory($file = NULL, array $data = NULL)
	{
		return new View($file, $data);
	}

	protected static function capture($hanariu_view_filename, array $hanariu_view_data)
	{
		extract($hanariu_view_data, EXTR_SKIP);

		if (View::$_global_data)
		{
			extract(View::$_global_data, EXTR_SKIP | EXTR_REFS);
		}

		ob_start();

		try
		{
			include $hanariu_view_filename;
		}
		catch (Exception $e)
		{
			ob_end_clean();
			throw $e;
		}

		return ob_get_clean();
	}

	public static function set_global($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $key2 => $value)
			{
				View::$_global_data[$key2] = $value;
			}
		}
		else
		{
			View::$_global_data[$key] = $value;
		}
	}

	public static function bind_global($key, & $value)
	{
		View::$_global_data[$key] =& $value;
	}

	protected $_file;
	protected $_data = array();

	public function __construct($file = NULL, array $data = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if ($data !== NULL)
		{
			// Add the values to the current data
			$this->_data = $data + $this->_data;
		}
	}

	public function & __get($key)
	{
		if (array_key_exists($key, $this->_data))
		{
			return $this->_data[$key];
		}
		elseif (array_key_exists($key, View::$_global_data))
		{
			return View::$_global_data[$key];
		}
		else
		{
			throw new Exception('View variable is not set: :var',
				array(':var' => $key));
		}
	}


	public function __set($key, $value)
	{
		$this->set($key, $value);
	}


	public function __isset($key)
	{
		return (isset($this->_data[$key]) OR isset(View::$_global_data[$key]));
	}


	public function __unset($key)
	{
		unset($this->_data[$key], View::$_global_data[$key]);
	}

	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (\Exception $e)
		{

			$error_response = Exception::_handler($e);
			return $error_response->body();
		}
	}

	public function set_filename($file)
	{
		if (($path = Hanariu::find_file('views', $file)) === FALSE)
		{
			throw new Exception('The requested view :file could not be found', array(
				':file' => $file,
			));
		}

		$this->_file = $path;

		return $this;
	}


	public function set($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $name => $value)
			{
				$this->_data[$name] = $value;
			}
		}
		else
		{
			$this->_data[$key] = $value;
		}

		return $this;
	}

	public function bind($key, & $value)
	{
		$this->_data[$key] =& $value;

		return $this;
	}

	public function render($file = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if (empty($this->_file))
		{
			throw new Exception('You must set the file to use within your view before rendering');
		}

		return View::capture($this->_file, $this->_data);
	}

}
