<?php namespace Hanariu\Core;

class Profiler {

	public static $rollover = 1000;
	protected static $_marks = array();

	public static function start($group, $name)
	{
		static $counter = 0;

		$token = 'kp/'.\base_convert($counter++, 10, 32);

		static::$_marks[$token] = array
		(
			'group' => \strtolower($group),
			'name'  => (string) $name,
			'start_time'   => \microtime(TRUE),
			'start_memory' => \memory_get_usage(),
			'stop_time'    => FALSE,
			'stop_memory'  => FALSE,
		);

		return $token;
	}

	public static function stop($token)
	{
		static::$_marks[$token]['stop_time']   = \microtime(TRUE);
		static::$_marks[$token]['stop_memory'] = \memory_get_usage();
	}

	public static function delete($token)
	{
		unset(static::$_marks[$token]);
	}

	public static function groups()
	{
		$groups = array();

		foreach (static::$_marks as $token => $mark)
		{
			$groups[$mark['group']][$mark['name']][] = $token;
		}

		return $groups;
	}

	public static function stats(array $tokens)
	{
		$min = $max = array(
			'time' => NULL,
			'memory' => NULL);

		$total = array(
			'time' => 0,
			'memory' => 0);

		foreach ($tokens as $token)
		{
			list($time, $memory) = static::total($token);

			if ($max['time'] === NULL OR $time > $max['time'])
			{
				$max['time'] = $time;
			}

			if ($min['time'] === NULL OR $time < $min['time'])
			{
				$min['time'] = $time;
			}

			$total['time'] += $time;

			if ($max['memory'] === NULL OR $memory > $max['memory'])
			{
				$max['memory'] = $memory;
			}

			if ($min['memory'] === NULL OR $memory < $min['memory'])
			{
				$min['memory'] = $memory;
			}

			$total['memory'] += $memory;
		}

		$count = \count($tokens);

		$average = array(
			'time' => $total['time'] / $count,
			'memory' => $total['memory'] / $count);

		return array(
			'min' => $min,
			'max' => $max,
			'total' => $total,
			'average' => $average);
	}

	public static function group_stats($groups = NULL)
	{
		$groups = ($groups === NULL)
			? static::groups()
			: \array_intersect_key(static::groups(), \array_flip( (array) $groups));

		$stats = array();

		foreach ($groups as $group => $names)
		{
			foreach ($names as $name => $tokens)
			{
				$_stats = static::stats($tokens);
				$stats[$group][$name] = $_stats['total'];
			}
		}

		$groups = array();

		foreach ($stats as $group => $names)
		{
			$groups[$group]['min'] = $groups[$group]['max'] = array(
				'time' => NULL,
				'memory' => NULL);

			$groups[$group]['total'] = array(
				'time' => 0,
				'memory' => 0);

			foreach ($names as $total)
			{
				if ( ! isset($groups[$group]['min']['time']) OR $groups[$group]['min']['time'] > $total['time'])
				{
					$groups[$group]['min']['time'] = $total['time'];
				}
				if ( ! isset($groups[$group]['min']['memory']) OR $groups[$group]['min']['memory'] > $total['memory'])
				{
					$groups[$group]['min']['memory'] = $total['memory'];
				}

				if ( ! isset($groups[$group]['max']['time']) OR $groups[$group]['max']['time'] < $total['time'])
				{
					$groups[$group]['max']['time'] = $total['time'];
				}
				if ( ! isset($groups[$group]['max']['memory']) OR $groups[$group]['max']['memory'] < $total['memory'])
				{
					$groups[$group]['max']['memory'] = $total['memory'];
				}

				$groups[$group]['total']['time']   += $total['time'];
				$groups[$group]['total']['memory'] += $total['memory'];
			}

			$count = \count($names);
			$groups[$group]['average']['time']   = $groups[$group]['total']['time'] / $count;
			$groups[$group]['average']['memory'] = $groups[$group]['total']['memory'] / $count;
		}

		return $groups;
	}

	public static function total($token)
	{
		$mark = static::$_marks[$token];

		if ($mark['stop_time'] === FALSE)
		{
			$mark['stop_time']   = \microtime(TRUE);
			$mark['stop_memory'] = \memory_get_usage();
		}

		return array
		(
			$mark['stop_time'] - $mark['start_time'],
			$mark['stop_memory'] - $mark['start_memory'],
		);
	}

	public static function application()
	{
		$stats = \Hanariu::cache('profiler_application_stats', NULL, 3600 * 24);

		if ( ! \is_array($stats) OR $stats['count'] > static::$rollover)
		{
			$stats = array(
				'min'   => array(
					'time'   => NULL,
					'memory' => NULL),
				'max'   => array(
					'time'   => NULL,
					'memory' => NULL),
				'total' => array(
					'time'   => NULL,
					'memory' => NULL),
				'count' => 0);
		}

		$time = \microtime(TRUE) - HANARIU_START_TIME;
		$memory = \memory_get_usage() - HANARIU_START_MEMORY;
		if ($stats['max']['time'] === NULL OR $time > $stats['max']['time'])
		{
			$stats['max']['time'] = $time;
		}
		if ($stats['min']['time'] === NULL OR $time < $stats['min']['time'])
		{
			$stats['min']['time'] = $time;
		}
		$stats['total']['time'] += $time;

		if ($stats['max']['memory'] === NULL OR $memory > $stats['max']['memory'])
		{
			$stats['max']['memory'] = $memory;
		}
		if ($stats['min']['memory'] === NULL OR $memory < $stats['min']['memory'])
		{
			$stats['min']['memory'] = $memory;
		}
		$stats['total']['memory'] += $memory;
		$stats['count']++;
		$stats['average'] = array(
			'time'   => $stats['total']['time'] / $stats['count'],
			'memory' => $stats['total']['memory'] / $stats['count']);
		
		\Hanariu::cache('profiler_application_stats', $stats);

		$stats['current']['time']   = $time;
		$stats['current']['memory'] = $memory;
		return $stats;
	}

}
