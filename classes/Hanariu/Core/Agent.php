<?php namespace Hanariu\Core;

class Agent {

	public static function user_agent($agent, $value)
	{
		if (is_array($value))
		{
			$data = array();
			foreach ($value as $part)
			{
				$data[$part] = self::user_agent($agent, $part);
			}

			return $data;
		}

		if ($value === 'browser' OR $value == 'version')
		{
			$info = array();
			$browsers = \Hanariu\Hanariu::$config->load('user_agents')->browser;

			foreach ($browsers as $search => $name)
			{
				if (\stripos($agent, $search) !== FALSE)
				{
					$info['browser'] = $name;

					if (\preg_match('#'.\preg_quote($search).'[^0-9.]*+([0-9.][0-9.a-z]*)#i', \Hanariu\Request::$user_agent, $matches))
					{
						$info['version'] = $matches[1];
					}
					else
					{
						$info['version'] = FALSE;
					}

					return $info[$value];
				}
			}
		}
		else
		{
			$group = \Hanariu\Hanariu::$config->load('user_agents')->$value;

			foreach ($group as $search => $name)
			{
				if (\stripos($agent, $search) !== FALSE)
				{

					return $name;
				}
			}
		}

		return FALSE;
	}

}
