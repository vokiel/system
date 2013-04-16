<?php namespace Hanariu\Log;

class StdOut extends Writer {

	public function write(array $messages)
	{
		foreach ($messages as $message)
		{
			fwrite(STDOUT, $this->format_message($message).PHP_EOL);
		}
	}

}
