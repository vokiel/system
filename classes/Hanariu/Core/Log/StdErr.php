<?php namespace Hanariu\Core\Log;

class StdErr extends \Hanariu\Log\Writer {

	public function write(array $messages)
	{
		foreach ($messages as $message)
		{
			\fwrite(STDERR, $this->format_message($message).PHP_EOL);
		}
	}

}
