<?php namespace Hanariu\Log;

class File extends Writer {

	protected $_directory;
	public function __construct($directory)
	{
		if ( ! \is_dir($directory) OR ! \is_writable($directory))
		{
			throw new \Hanariu\Exception('Directory :dir must be writable',
				array(':dir' => \Hanariu\Debug::path($directory)));
		}

		$this->_directory = realpath($directory).DIRECTORY_SEPARATOR;
	}

	public function write(array $messages)
	{
		$directory = $this->_directory.\date('Y');

		if ( ! \is_dir($directory))
		{
			\mkdir($directory, 02777);
			\chmod($directory, 02777);
		}

		$directory .= DIRECTORY_SEPARATOR.\date('m');

		if ( ! \is_dir($directory))
		{
			\mkdir($directory, 02777);
			\chmod($directory, 02777);
		}

		$filename = $directory.DIRECTORY_SEPARATOR.\date('d').EXT;

		if ( ! \file_exists($filename))
		{
			\file_put_contents($filename, PHP_EOL);
			\chmod($filename, 0666);
		}

		foreach ($messages as $message)
		{
			\file_put_contents($filename, PHP_EOL.$this->format_message($message), FILE_APPEND);
		}
	}

}
