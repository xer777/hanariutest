<?php namespace Core;

class Date {

	const YEAR   = 31556926;
	const MONTH  = 2629744;
	const WEEK   = 604800;
	const DAY    = 86400;
	const HOUR   = 3600;
	const MINUTE = 60;
	const MONTHS_LONG  = '%B';
	const MONTHS_SHORT = '%b';


	public static $timestamp_format = 'Y-m-d H:i:s';
	public static $timezone;

	public static function offset($remote, $local = NULL, $now = NULL)
	{
		if ($local === NULL)
		{
			$local = \date_default_timezone_get();
		}

		if (\is_int($now))
		{
			$now = \date(\DateTime::RFC2822, $now);
		}

		$zone_remote = new \DateTimeZone($remote);
		$zone_local  = new \DateTimeZone($local);
		$time_remote = new \DateTime($now, $zone_remote);
		$time_local  = new \DateTime($now, $zone_local);
		$offset = $zone_remote->getOffset($time_remote) - $zone_local->getOffset($time_local);

		return $offset;
	}


	public static function seconds($step = 1, $start = 0, $end = 60)
	{
		// Always integer
		$step = (int) $step;

		$seconds = array();

		for ($i = $start; $i < $end; $i += $step)
		{
			$seconds[$i] = \sprintf('%02d', $i);
		}

		return $seconds;
	}

	public static function minutes($step = 5)
	{
		return \Core\Date::seconds($step);
	}


	public static function hours($step = 1, $long = FALSE, $start = NULL)
	{
		// Default values
		$step = (int) $step;
		$long = (bool) $long;
		$hours = array();


		if ($start === NULL)
		{
			$start = ($long === FALSE) ? 1 : 0;
		}

		$hours = array();
		$size = ($long === TRUE) ? 23 : 12;

		for ($i = $start; $i <= $size; $i += $step)
		{
			$hours[$i] = (string) $i;
		}

		return $hours;
	}

	public static function ampm($hour)
	{
		// Always integer
		$hour = (int) $hour;

		return ($hour > 11) ? 'PM' : 'AM';
	}

	public static function adjust($hour, $ampm)
	{
		$hour = (int) $hour;
		$ampm = \strtolower($ampm);

		switch ($ampm)
		{
			case 'am':
				if ($hour == 12)
				{
					$hour = 0;
				}
			break;
			case 'pm':
				if ($hour < 12)
				{
					$hour += 12;
				}
			break;
		}

		return \sprintf('%02d', $hour);
	}

	public static function days($month, $year = FALSE)
	{
		static $months;

		if ($year === FALSE)
		{
			$year = \date('Y');
		}

		$month = (int) $month;
		$year  = (int) $year;

		if (empty($months[$year][$month]))
		{
			$months[$year][$month] = array();
			$total = \date('t', \mktime(1, 0, 0, $month, 1, $year)) + 1;

			for ($i = 1; $i < $total; $i++)
			{
				$months[$year][$month][$i] = (string) $i;
			}
		}

		return $months[$year][$month];
	}

	public static function months($format = NULL)
	{
		$months = array();

		if ($format === \Core\Date::MONTHS_LONG OR $format === \Core\Date::MONTHS_SHORT)
		{
			for ($i = 1; $i <= 12; ++$i)
			{
				$months[$i] = \strftime($format, \mktime(0, 0, 0, $i, 1));
			}
		}
		else
		{
			$months = \Core\Date::hours();
		}

		return $months;
	}

	public static function years($start = FALSE, $end = FALSE)
	{
		$start = ($start === FALSE) ? (\date('Y') - 5) : (int) $start;
		$end   = ($end   === FALSE) ? (\date('Y') + 5) : (int) $end;

		$years = array();

		for ($i = $start; $i <= $end; $i++)
		{
			$years[$i] = (string) $i;
		}

		return $years;
	}

	public static function span($remote, $local = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		$output = \trim(\strtolower( (string) $output));

		if ( ! $output)
		{
			return FALSE;
		}

		$output = \preg_split('/[^a-z]+/', $output);
		$output = \array_combine($output, \array_fill(0, \count($output), 0));
		\extract(\array_flip($output), EXTR_SKIP);

		if ($local === NULL)
		{
			$local = time();
		}

		// Calculate timespan (seconds)
		$timespan = \abs($remote - $local);

		if (isset($output['years']))
		{
			$timespan -= \Core\Date::YEAR * ($output['years'] = (int) \floor($timespan / Date::YEAR));
		}

		if (isset($output['months']))
		{
			$timespan -= \Core\Date::MONTH * ($output['months'] = (int) \floor($timespan / Date::MONTH));
		}

		if (isset($output['weeks']))
		{
			$timespan -= \Core\Date::WEEK * ($output['weeks'] = (int) \floor($timespan / Date::WEEK));
		}

		if (isset($output['days']))
		{
			$timespan -= \Core\Date::DAY * ($output['days'] = (int) \floor($timespan / Date::DAY));
		}

		if (isset($output['hours']))
		{
			$timespan -= \Core\Date::HOUR * ($output['hours'] = (int) \floor($timespan / Date::HOUR));
		}

		if (isset($output['minutes']))
		{
			$timespan -= \Core\Date::MINUTE * ($output['minutes'] = (int) \floor($timespan / Date::MINUTE));
		}

		if (isset($output['seconds']))
		{
			$output['seconds'] = $timespan;
		}

		if (\count($output) === 1)
		{
			return \array_pop($output);
		}

		return $output;
	}

	public static function fuzzy_span($timestamp, $local_timestamp = NULL)
	{
		$local_timestamp = ($local_timestamp === NULL) ? \time() : (int) $local_timestamp;

		// Determine the difference in seconds
		$offset = \abs($local_timestamp - $timestamp);

		if ($offset <= \Core\Date::MINUTE)
		{
			$span = 'moments';
		}
		elseif ($offset < (\Core\Date::MINUTE * 20))
		{
			$span = 'a few minutes';
		}
		elseif ($offset < \Core\Date::HOUR)
		{
			$span = 'less than an hour';
		}
		elseif ($offset < (\Core\Date::HOUR * 4))
		{
			$span = 'a couple of hours';
		}
		elseif ($offset < \Core\Date::DAY)
		{
			$span = 'less than a day';
		}
		elseif ($offset < (\Core\Date::DAY * 2))
		{
			$span = 'about a day';
		}
		elseif ($offset < (\Core\Date::DAY * 4))
		{
			$span = 'a couple of days';
		}
		elseif ($offset < \Core\Date::WEEK)
		{
			$span = 'less than a week';
		}
		elseif ($offset < (\Core\Date::WEEK * 2))
		{
			$span = 'about a week';
		}
		elseif ($offset < \Core\Date::MONTH)
		{
			$span = 'less than a month';
		}
		elseif ($offset < (\Core\Date::MONTH * 2))
		{
			$span = 'about a month';
		}
		elseif ($offset < (\Core\Date::MONTH * 4))
		{
			$span = 'a couple of months';
		}
		elseif ($offset < \Core\Date::YEAR)
		{
			$span = 'less than a year';
		}
		elseif ($offset < (\Core\Date::YEAR * 2))
		{
			$span = 'about a year';
		}
		elseif ($offset < (\Core\Date::YEAR * 4))
		{
			$span = 'a couple of years';
		}
		elseif ($offset < (\Core\Date::YEAR * 8))
		{
			$span = 'a few years';
		}
		elseif ($offset < (\Core\Date::YEAR * 12))
		{
			$span = 'about a decade';
		}
		elseif ($offset < (\Core\Date::YEAR * 24))
		{
			$span = 'a couple of decades';
		}
		elseif ($offset < (\Core\Date::YEAR * 64))
		{
			$span = 'several decades';
		}
		else
		{
			$span = 'a long time';
		}

		if ($timestamp <= $local_timestamp)
		{

			return $span.' ago';
		}
		else
		{
			return 'in '.$span;
		}
	}


	public static function unix2dos($timestamp = FALSE)
	{
		$timestamp = ($timestamp === FALSE) ? \getdate() : \getdate($timestamp);

		if ($timestamp['year'] < 1980)
		{
			return (1 << 21 | 1 << 16);
		}

		$timestamp['year'] -= 1980;

		return ($timestamp['year']    << 25 | $timestamp['mon']     << 21 |
		        $timestamp['mday']    << 16 | $timestamp['hours']   << 11 |
		        $timestamp['minutes'] << 5  | $timestamp['seconds'] >> 1);
	}

	public static function dos2unix($timestamp = FALSE)
	{
		$sec  = 2 * ($timestamp & 0x1f);
		$min  = ($timestamp >>  5) & 0x3f;
		$hrs  = ($timestamp >> 11) & 0x1f;
		$day  = ($timestamp >> 16) & 0x1f;
		$mon  = ($timestamp >> 21) & 0x0f;
		$year = ($timestamp >> 25) & 0x7f;

		return \mktime($hrs, $min, $sec, $mon, $day, $year + 1980);
	}

	public static function formatted_time($datetime_str = 'now', $timestamp_format = NULL, $timezone = NULL)
	{
		$timestamp_format = ($timestamp_format == NULL) ? \Core\Date::$timestamp_format : $timestamp_format;
		$timezone         = ($timezone === NULL) ? \Core\Date::$timezone : $timezone;

		$tz   = new \DateTimeZone($timezone ? $timezone : \date_default_timezone_get());
		$time = new \DateTime($datetime_str, $tz);

		if ($time->getTimeZone()->getName() !== $tz->getName())
		{
			$time->setTimeZone($tz);
		}

		return $time->format($timestamp_format);
	}

}
