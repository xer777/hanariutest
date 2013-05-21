<?php namespace Core;

class Num {

	const ROUND_HALF_UP		= 1;
	const ROUND_HALF_DOWN	= 2;
	const ROUND_HALF_EVEN	= 3;
	const ROUND_HALF_ODD	= 4;

	public static $byte_units = array
	(
		'B'   => 0,
		'K'   => 10,
		'Ki'  => 10,
		'KB'  => 10,
		'KiB' => 10,
		'M'   => 20,
		'Mi'  => 20,
		'MB'  => 20,
		'MiB' => 20,
		'G'   => 30,
		'Gi'  => 30,
		'GB'  => 30,
		'GiB' => 30,
		'T'   => 40,
		'Ti'  => 40,
		'TB'  => 40,
		'TiB' => 40,
		'P'   => 50,
		'Pi'  => 50,
		'PB'  => 50,
		'PiB' => 50,
		'E'   => 60,
		'Ei'  => 60,
		'EB'  => 60,
		'EiB' => 60,
		'Z'   => 70,
		'Zi'  => 70,
		'ZB'  => 70,
		'ZiB' => 70,
		'Y'   => 80,
		'Yi'  => 80,
		'YB'  => 80,
		'YiB' => 80,
	);


	public static function ordinal($number)
	{
		if ($number % 100 > 10 AND $number % 100 < 14)
		{
			return 'th';
		}

		switch ($number % 10)
		{
			case 1:
				return 'st';
			case 2:
				return 'nd';
			case 3:
				return 'rd';
			default:
				return 'th';
		}
	}

	public static function format($number, $places, $monetary = FALSE)
	{
		$info = \localeconv();

		if ($monetary)
		{
			$decimal   = $info['mon_decimal_point'];
			$thousands = $info['mon_thousands_sep'];
		}
		else
		{
			$decimal   = $info['decimal_point'];
			$thousands = $info['thousands_sep'];
		}

		return \number_format($number, $places, $decimal, $thousands);
	}

	public static function round($value, $precision = 0, $mode = self::ROUND_HALF_UP, $native = TRUE)
	{
		if (\version_compare(PHP_VERSION, '5.3', '>=') AND $native)
		{
			return round($value, $precision, $mode);
		}

		if ($mode === self::ROUND_HALF_UP)
		{
			return \round($value, $precision);
		}
		else
		{
			$factor = ($precision === 0) ? 1 : pow(10, $precision);

			switch ($mode)
			{
				case self::ROUND_HALF_DOWN:
				case self::ROUND_HALF_EVEN:
				case self::ROUND_HALF_ODD:
					// Check if we have a rounding tie, otherwise we can just call round()
					if (($value * $factor) - \floor($value * $factor) === 0.5)
					{
						if ($mode === self::ROUND_HALF_DOWN)
						{
							$up = ($value < 0);
						}
						else
						{
							$up = ( ! ( ! (\floor($value * $factor) & 1)) === ($mode === self::ROUND_HALF_EVEN));
						}

						if ($up)
						{
							$value = \ceil($value * $factor);
						}
						else
						{
							$value = \floor($value * $factor);
						}
						return $value / $factor;
					}
					else
					{
						return \round($value, $precision);
					}
				break;
			}
		}
	}

	public static function bytes($size)
	{
		$size = \trim( (string) $size);
		$accepted = \implode('|', \array_keys(\Core\Num::$byte_units));
		$pattern = '/^([0-9]+(?:\.[0-9]+)?)('.$accepted.')?$/Di';
		if ( ! \preg_match($pattern, $size, $matches))
			throw new \Core_Exception('The byte unit size, ":size", is improperly formatted.', array(
				':size' => $size,
			));

		$size = (float) $matches[1];
		$unit = \Arr::get($matches, 2, 'B');
		$bytes = $size * \pow(2, \Core\Num::$byte_units[$unit]);
		return $bytes;
	}

}
