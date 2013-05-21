<?php namespace Core;

class Valid {

	public static function not_empty($value)
	{
		if (\is_object($value) AND $value instanceof \ArrayObject)
		{
			$value = $value->getArrayCopy();
		}

		return ! \in_array($value, array(NULL, FALSE, '', array()), TRUE);
	}

	public static function regex($value, $expression)
	{
		return (bool) \preg_match($expression, (string) $value);
	}

	public static function min_length($value, $length)
	{
		return \Core\UTF8::strlen($value) >= $length;
	}


	public static function max_length($value, $length)
	{
		return \Core\UTF8::strlen($value) <= $length;
	}


	public static function exact_length($value, $length)
	{
		if (\is_array($length))
		{
			foreach ($length as $strlen)
			{
				if (\Core\UTF8::strlen($value) === $strlen)
					return TRUE;
			}
			return FALSE;
		}

		return \Core\UTF8::strlen($value) === $length;
	}

	public static function equals($value, $required)
	{
		return ($value === $required);
	}


	public static function email($email, $strict = FALSE)
	{
		if (\Core\UTF8::strlen($email) > 254)
		{
			return FALSE;
		}

		if ($strict === TRUE)
		{
			$qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
			$dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
			$atom  = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
			$pair  = '\\x5c[\\x00-\\x7f]';

			$domain_literal = "\\x5b($dtext|$pair)*\\x5d";
			$quoted_string  = "\\x22($qtext|$pair)*\\x22";
			$sub_domain     = "($atom|$domain_literal)";
			$word           = "($atom|$quoted_string)";
			$domain         = "$sub_domain(\\x2e$sub_domain)*";
			$local_part     = "$word(\\x2e$word)*";

			$expression     = "/^$local_part\\x40$domain$/D";
		}
		else
		{
			$expression = '/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})$/iD';
		}

		return (bool) \preg_match($expression, (string) $email);
	}


	public static function email_domain($email)
	{
		if ( ! \Core\Valid::not_empty($email))
			return FALSE; 

		return (bool) checkdnsrr(\preg_replace('/^[^@]++@/', '', $email), 'MX');
	}


	public static function url($url)
	{

		if ( ! preg_match(
			'~^

			# scheme
			[-a-z0-9+.]++://

			# username:password (optional)
			(?:
				    [-a-z0-9$_.+!*\'(),;?&=%]++   # username
				(?::[-a-z0-9$_.+!*\'(),;?&=%]++)? # password (optional)
				@
			)?

			(?:
				# ip address
				\d{1,3}+(?:\.\d{1,3}+){3}+

				| # or

				# hostname (captured)
				(
					     (?!-)[-a-z0-9]{1,63}+(?<!-)
					(?:\.(?!-)[-a-z0-9]{1,63}+(?<!-)){0,126}+
				)
			)

			# port (optional)
			(?::\d{1,5}+)?

			# path (optional)
			(?:/.*)?

			$~iDx', $url, $matches))
			return FALSE;

		if ( ! isset($matches[1]))
			return TRUE;

		if (\strlen($matches[1]) > 253)
			return FALSE;

		$tld = \ltrim(\substr($matches[1], (int) \strrpos($matches[1], '.')), '.');
		return \ctype_alpha($tld[0]);
	}

	public static function ip($ip, $allow_private = TRUE)
	{
		$flags = FILTER_FLAG_NO_RES_RANGE;

		if ($allow_private === FALSE)
		{
			$flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
		}

		return (bool) \filter_var($ip, FILTER_VALIDATE_IP, $flags);
	}

	public static function credit_card($number, $type = NULL)
	{
		if (($number = \preg_replace('/\D+/', '', $number)) === '')
			return FALSE;

		if ($type == NULL)
		{
			$type = 'default';
		}
		elseif (\is_array($type))
		{
			foreach ($type as $t)
			{
				if (\Core\Valid::credit_card($number, $t))
					return TRUE;
			}

			return FALSE;
		}

		$cards = \Hanariu::$config->load('credit_cards');
		$type = \strtolower($type);

		if ( ! isset($cards[$type]))
			return FALSE;

		$length = \strlen($number);

		if ( ! \in_array($length, \preg_split('/\D+/', $cards[$type]['length'])))
			return FALSE;

		if ( ! \preg_match('/^'.$cards[$type]['prefix'].'/', $number))
			return FALSE;

		if ($cards[$type]['luhn'] == FALSE)
			return TRUE;

		return \Core\Valid::luhn($number);
	}

	public static function luhn($number)
	{

		$number = (string) $number;

		if ( ! \ctype_digit($number))
		{
			return FALSE;
		}

		$length = \strlen($number);
		$checksum = 0;

		for ($i = $length - 1; $i >= 0; $i -= 2)
		{
			$checksum += \substr($number, $i, 1);
		}

		for ($i = $length - 2; $i >= 0; $i -= 2)
		{
			$double = \substr($number, $i, 1) * 2;
			$checksum += ($double >= 10) ? ($double - 9) : $double;
		}

		return ($checksum % 10 === 0);
	}


	public static function phone($number, $lengths = NULL)
	{
		if ( ! \is_array($lengths))
		{
			$lengths = array(7,10,11);
		}

		$number = \preg_replace('/\D+/', '', $number);
		return \in_array(\strlen($number), $lengths);
	}

	public static function date($str)
	{
		return (\strtotime($str) !== FALSE);
	}


	public static function alpha($str, $utf8 = FALSE)
	{
		$str = (string) $str;

		if ($utf8 === TRUE)
		{
			return (bool) \preg_match('/^\pL++$/uD', $str);
		}
		else
		{
			return \ctype_alpha($str);
		}
	}

	public static function alpha_numeric($str, $utf8 = FALSE)
	{
		if ($utf8 === TRUE)
		{
			return (bool) \preg_match('/^[\pL\pN]++$/uD', $str);
		}
		else
		{
			return \ctype_alnum($str);
		}
	}

	public static function alpha_dash($str, $utf8 = FALSE)
	{
		if ($utf8 === TRUE)
		{
			$regex = '/^[-\pL\pN_]++$/uD';
		}
		else
		{
			$regex = '/^[-a-z0-9_]++$/iD';
		}

		return (bool) \preg_match($regex, $str);
	}


	public static function digit($str, $utf8 = FALSE)
	{
		if ($utf8 === TRUE)
		{
			return (bool) \preg_match('/^\pN++$/uD', $str);
		}
		else
		{
			return (\is_int($str) AND $str >= 0) OR \ctype_digit($str);
		}
	}


	public static function numeric($str)
	{
		list($decimal) = \array_values(\localeconv());
		return (bool) \preg_match('/^-?+(?=.*[0-9])[0-9]*+'.\preg_quote($decimal).'?+[0-9]*+$/D', (string) $str);
	}

	public static function range($number, $min, $max, $step = NULL)
	{
		if ($number <= $min OR $number >= $max)
		{
			return FALSE;
		}

		if ( ! $step)
		{
			$step = 1;
		}

		return (($number - $min) % $step === 0);
	}


	public static function decimal($str, $places = 2, $digits = NULL)
	{
		if ($digits > 0)
		{
			$digits = '{'.( (int) $digits).'}';
		}
		else
		{
			$digits = '+';
		}

		list($decimal) = \array_values(\localeconv());

		return (bool) \preg_match('/^[+-]?[0-9]'.$digits.\preg_quote($decimal).'[0-9]{'.( (int) $places).'}$/D', $str);
	}

	public static function color($str)
	{
		return (bool) \preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $str);
	}

	public static function matches($array, $field, $match)
	{
		return ($array[$field] === $array[$match]);
	}
}
