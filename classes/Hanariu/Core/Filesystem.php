<?php namespace Hanariu\Core;

class Filesystem
{
	protected $_include_paths = array();

	public function __construct(array $paths)
	{
		$this->_paths = $paths;
	}

	public function include_paths()
	{
		return $this->_paths;
	}

	public function list_files($directory, array $paths = NULL)
	{
		$found = array();

		foreach ($paths === NULL ? $this->_paths : $paths as $path)
		{
			if (is_dir($path.$directory))
			{
				$dir = new \DirectoryIterator($path.$directory);

				foreach ($dir as $file)
				{
					$filename = $file->getFilename();

					// Skip all hidden files and UNIX backup files
					if ($filename[0] === '.' OR $filename[strlen($filename)-1] === '~')
						continue;

					// Relative filename is the array key
					$key = $directory.'/'.$filename;

					if ($file->isDir())
					{
						if ($sub_dir = $this->list_files($key, $paths))
						{
							if (isset($found[$key]))
							{
								// Append the sub-directory list
								$found[$key] += $sub_dir;
							}
							else
							{
								// Create a new sub-directory list
								$found[$key] = $sub_dir;
							}
						}
					}
					else
					{
						if ( ! isset($found[$key]))
						{
							$found[$key] = $this->realpath($file->getPathName());
						}
					}
				}
			}
		}

		return $found;
	}

	public function find_file($dir, $file, $ext = 'php')
	{
		$found = FALSE;

		$path = $this->_build_file_path($dir, $file, $ext);

		foreach ($this->_paths as $dir)
		{
			if (is_file($dir.$path))
			{
				$found = $dir.$path;
				break;
			}
		}

		return $found;
	}

	public function find_all_files($dir, $file, $ext = 'php')
	{
		$found = array();

		$path = $this->_build_file_path($dir, $file, $ext);

		foreach ($this->_paths as $dir)
		{
			if (is_file($dir.$path))
				$found[] = $dir.$path;
		}

		return $found;
	}

	public function load($file)
	{
		return include $file;
	}

	public function realpath($path)
	{
		return realpath($path);
	}

	protected function _build_file_path($dir, $file, $ext)
	{
		return "$dir/$file.$ext";
	}
}
