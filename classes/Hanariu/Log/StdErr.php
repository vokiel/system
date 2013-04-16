<?php namespace Hanariu\Log;

class StdErr extends Writer {

	public function write(array $messages)
	{
		foreach ($messages as $message)
		{
			fwrite(STDERR, $this->format_message($message).PHP_EOL);
		}
	}

}
