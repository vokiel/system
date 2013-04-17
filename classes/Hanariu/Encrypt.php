<?php namespace Hanariu;

class Encrypt {

	public static $default = 'default';
	public static $instances = array();
	protected static $_rand;

	public static function instance($name = NULL)
	{
		if ($name === NULL)
		{
			$name = Encrypt::$default;
		}

		if ( ! isset(Encrypt::$instances[$name]))
		{
			$config = Hanariu::$config->load('encrypt')->$name;

			if ( ! isset($config['key']))
			{
				throw new Exception('No encryption key is defined in the encryption configuration group: :group',
					array(':group' => $name));
			}

			if ( ! isset($config['mode']))
			{
				$config['mode'] = MCRYPT_MODE_NOFB;
			}

			if ( ! isset($config['cipher']))
			{
				$config['cipher'] = MCRYPT_RIJNDAEL_128;
			}

			Encrypt::$instances[$name] = new Encrypt($config['key'], $config['mode'], $config['cipher']);
		}

		return Encrypt::$instances[$name];
	}

	public function __construct($key, $mode, $cipher)
	{
		$size = mcrypt_get_key_size($cipher, $mode);

		if (isset($key[$size]))
		{
			$key = substr($key, 0, $size);
		}

		$this->_key    = $key;
		$this->_mode   = $mode;
		$this->_cipher = $cipher;
		$this->_iv_size = mcrypt_get_iv_size($this->_cipher, $this->_mode);
	}


	public function encode($data)
	{
		if (Encrypt::$_rand === NULL)
		{
			if (Hanariu::$is_windows)
			{
				Encrypt::$_rand = MCRYPT_RAND;
			}
			else
			{
				if (defined('MCRYPT_DEV_URANDOM'))
				{
					Encrypt::$_rand = MCRYPT_DEV_URANDOM;
				}
				elseif (defined('MCRYPT_DEV_RANDOM'))
				{
					Encrypt::$_rand = MCRYPT_DEV_RANDOM;
				}
				else
				{
					Encrypt::$_rand = MCRYPT_RAND;
				}
			}
		}

		if (Encrypt::$_rand === MCRYPT_RAND)
		{
			mt_srand();
		}

		$iv = mcrypt_create_iv($this->_iv_size, Encrypt::$_rand);
		$data = mcrypt_encrypt($this->_cipher, $this->_key, $data, $this->_mode, $iv);
		return base64_encode($iv.$data);
	}


	public function decode($data)
	{
		$data = base64_decode($data, TRUE);

		if ( ! $data)
		{
			return FALSE;
		}
		$iv = substr($data, 0, $this->_iv_size);

		if ($this->_iv_size !== strlen($iv))
		{
			return FALSE;
		}

		$data = substr($data, $this->_iv_size);
		return rtrim(mcrypt_decrypt($this->_cipher, $this->_key, $data, $this->_mode, $iv), "\0");
	}

}
