<?php namespace Hanariu\Response;

class Send extends \Hanariu\Response {

	public function file($filename, $download = NULL, array $options = NULL)
	{
		if ( ! empty($options['mime_type']))
		{

			$mime = $options['mime_type'];
		}

		if ($filename === TRUE)
		{
			if (empty($download))
			{
				throw new \Hanariu\Exception('Download name must be provided for streaming files');
			}

			$options['delete'] = FALSE;

			if ( ! isset($mime))
			{
				$mime = \Hanariu\File::mime_by_ext(\strtolower(\pathinfo($download, PATHINFO_EXTENSION)));
			}

			$file_data = (string) $this->_body;
			$size = \strlen($file_data);
			$file = \tmpfile();
			\fwrite($file, $file_data);
			unset($file_data);
		}
		else
		{
			$filename = \realpath($filename);

			if (empty($download))
			{
				$download = \pathinfo($filename, PATHINFO_BASENAME);
			}

			$size = \filesize($filename);

			if ( ! isset($mime))
			{
				$mime = \Hanariu\File::mime_by_ext(\pathinfo($download, PATHINFO_EXTENSION));
			}

			$file = \fopen($filename, 'rb');
		}

		if ( ! \is_resource($file))
		{
			throw new \Hanariu\Exception('Could not read file to send: :file', array(
				':file' => $download,
			));
		}

		$disposition = empty($options['inline']) ? 'attachment' : 'inline';
		list($start, $end) = $this->_calculate_byte_range($size);

		if ( ! empty($options['resumable']))
		{
			if ($start > 0 OR $end < ($size - 1))
			{
				$this->_status = 206;
			}

			$this->_header['content-range'] = 'bytes '.$start.'-'.$end.'/'.$size;
			$this->_header['accept-ranges'] = 'bytes';
		}

		$this->_header['content-disposition'] = $disposition.'; filename="'.$download.'"';
		$this->_header['content-type']        = $mime;
		$this->_header['content-length']      = (string) (($end - $start) + 1);

		if (\Hanariu\Request::user_agent('browser') === 'Internet Explorer')
		{
			if (\Hanariu\Request::$initial->secure())
			{
				$this->_header['pragma'] = $this->_header['cache-control'] = 'public';
			}

			if (\version_compare(\Hanariu\Request::user_agent('version'), '8.0', '>='))
			{
				$this->_header['x-content-type-options'] = 'nosniff';
			}
		}

		$this->send_headers();

		while (\ob_get_level())
		{
			\ob_end_flush();
		}

		\ignore_user_abort(TRUE);

		if ( ! \Hanariu\Hanariu::$safe_mode)
		{
			\set_time_limit(0);
		}

		$block = 1024 * 16;

		\fseek($file, $start);

		while ( ! \feof($file) AND ($pos = \ftell($file)) <= $end)
		{
			if (\connection_aborted())
				break;

			if ($pos + $block > $end)
			{
				$block = $end - $pos + 1;
			}

			echo \fread($file, $block);
			\flush();
		}

		\fclose($file);

		if ( ! empty($options['delete']))
		{
			try
			{
				\unlink($filename);
			}
			catch (\Exception $e)
			{
				$error = \Hanariu\Exception::text($e);

				if (\is_object(\Hanariu\Hanariu::$log))
				{
					\Hanariu\Hanariu::$log->add(\Hanariu\Log::ERROR, $error);
					\Hanariu\Hanariu::$log->write();
				}
			}
		}

		exit;
	}
}
