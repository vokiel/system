<?php namespace Hanariu\Core\Log;

class StdOut extends \Hanariu\Log\Writer {

	public function write(array $messages)
	{
		foreach ($messages as $message)
		{
			\fwrite(STDOUT, $this->format_message($message).PHP_EOL);
		}
	}

}
