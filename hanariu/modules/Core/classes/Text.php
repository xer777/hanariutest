<?php namespace Core;

class Text {

	public static $units = array(
		1000000000 => 'billion',
		1000000    => 'million',
		1000       => 'thousand',
		100        => 'hundred',
		90 => 'ninety',
		80 => 'eighty',
		70 => 'seventy',
		60 => 'sixty',
		50 => 'fifty',
		40 => 'fourty',
		30 => 'thirty',
		20 => 'twenty',
		19 => 'nineteen',
		18 => 'eighteen',
		17 => 'seventeen',
		16 => 'sixteen',
		15 => 'fifteen',
		14 => 'fourteen',
		13 => 'thirteen',
		12 => 'twelve',
		11 => 'eleven',
		10 => 'ten',
		9  => 'nine',
		8  => 'eight',
		7  => 'seven',
		6  => 'six',
		5  => 'five',
		4  => 'four',
		3  => 'three',
		2  => 'two',
		1  => 'one',
	);

	public static function limit_words($str, $limit = 100, $end_char = NULL)
	{
		$limit = (int) $limit;
		$end_char = ($end_char === NULL) ? '…' : $end_char;

		if (\trim($str) === '')
			return $str;

		if ($limit <= 0)
			return $end_char;

		\preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $str, $matches);
		return \rtrim($matches[0]).((\strlen($matches[0]) === \strlen($str)) ? '' : $end_char);
	}


	public static function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
	{
		$end_char = ($end_char === NULL) ? '…' : $end_char;

		$limit = (int) $limit;

		if (\trim($str) === '' OR \Core\UTF8::strlen($str) <= $limit)
			return $str;

		if ($limit <= 0)
			return $end_char;

		if ($preserve_words === FALSE)
			return \rtrim(\Core\UTF8::substr($str, 0, $limit)).$end_char;

		if ( ! \preg_match('/^.{0,'.$limit.'}\s/us', $str, $matches))
			return $end_char;

		return \rtrim($matches[0]).((\strlen($matches[0]) === \strlen($str)) ? '' : $end_char);
	}

	public static function ucfirst($string, $delimiter = '-')
	{
		return \implode($delimiter, \array_map('ucfirst', \explode($delimiter, $string)));
	}

	public static function reduce_slashes($str)
	{
		return \preg_replace('#(?<!:)//+#', '/', $str);
	}

	public static function auto_link($text)
	{
		return \Core\Text::auto_link_urls(\Core\Text::auto_link_emails($text));
	}

	public static function auto_link_urls($text)
	{
		$text = \preg_replace_callback('~\b(?<!href="|">)(?:ht|f)tps?://[^<\s]+(?:/|\b)~i', 'Text::_auto_link_urls_callback1', $text);
		return \preg_replace_callback('~\b(?<!://|">)www(?:\.[a-z0-9][-a-z0-9]*+)+\.[a-z]{2,6}[^<\s]*\b~i', 'Text::_auto_link_urls_callback2', $text);
	}

	protected static function _auto_link_urls_callback1($matches)
	{
		return \Core\HTML::anchor($matches[0]);
	}

	protected static function _auto_link_urls_callback2($matches)
	{
		return \Core\HTML::anchor('http://'.$matches[0], $matches[0]);
	}

	public static function auto_link_emails($text)
	{
		return \preg_replace_callback('~\b(?<!href="mailto:|58;)(?!\.)[-+_a-z0-9.]++(?<!\.)@(?![-.])[-a-z0-9.]+(?<!\.)\.[a-z]{2,6}\b(?!</a>)~i', 'Text::_auto_link_emails_callback', $text);
	}

	protected static function _auto_link_emails_callback($matches)
	{
		return \Core\HTML::mailto($matches[0]);
	}

	public static function auto_p($str, $br = TRUE)
	{
		if (($str = \trim($str)) === '')
			return '';
		$str = \str_replace(array("\r\n", "\r"), "\n", $str);
		$str = \preg_replace('~^[ \t]+~m', '', $str);
		$str = \preg_replace('~[ \t]+$~m', '', $str);

		if ($html_found = (\strpos($str, '<') !== FALSE))
		{
			$no_p = '(?:p|div|h[1-6r]|ul|ol|li|blockquote|d[dlt]|pre|t[dhr]|t(?:able|body|foot|head)|c(?:aption|olgroup)|form|s(?:elect|tyle)|a(?:ddress|rea)|ma(?:p|th))';

			$str = \preg_replace('~^<'.$no_p.'[^>]*+>~im', "\n$0", $str);
			$str = \preg_replace('~</'.$no_p.'\s*+>$~im', "$0\n", $str);
		}

		$str = '<p>'.trim($str).'</p>';
		$str = \preg_replace('~\n{2,}~', "</p>\n\n<p>", $str);

		if ($html_found !== FALSE)
		{
			$str = \preg_replace('~<p>(?=</?'.$no_p.'[^>]*+>)~i', '', $str);
			$str = \preg_replace('~(</?'.$no_p.'[^>]*+>)</p>~i', '$1', $str);
		}

		if ($br === TRUE)
		{
			$str = \preg_replace('~(?<!\n)\n(?!\n)~', "<br />\n", $str);
		}

		return $str;
	}


	public static function bytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE)
	{
		$format = ($format === NULL) ? '%01.2f %s' : (string) $format;
		if ($si == FALSE OR \strpos($force_unit, 'i') !== FALSE)
		{
			$units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
			$mod   = 1024;
		}
		else
		{
			$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
			$mod   = 1000;
		}

		if (($power = \array_search( (string) $force_unit, $units)) === FALSE)
		{
			$power = ($bytes > 0) ? \floor(\log($bytes, $mod)) : 0;
		}

		return \sprintf($format, $bytes / \pow($mod, $power), $units[$power]);
	}

	public static function alternate()
	{
		static $i;

		if (\func_num_args() === 0)
		{
			$i = 0;
			return '';
		}

		$args = \func_get_args();
		return $args[($i++ % \count($args))];
	}

	public static function random($type = NULL, $length = 8)
	{
		if ($type === NULL)
		{
			$type = 'alnum';
		}

		$utf8 = FALSE;

		switch ($type)
		{
			case 'alnum':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'hexdec':
				$pool = '0123456789abcdef';
			break;
			case 'numeric':
				$pool = '0123456789';
			break;
			case 'nozero':
				$pool = '123456789';
			break;
			case 'distinct':
				$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
			break;
			default:
				$pool = (string) $type;
				$utf8 = ! \Core\UTF8::is_ascii($pool);
			break;
		}

		$pool = ($utf8 === TRUE) ? \Core\UTF8::str_split($pool, 1) : \str_split($pool, 1);
		$max = \count($pool) - 1;

		$str = '';
		for ($i = 0; $i < $length; $i++)
		{
			$str .= $pool[\mt_rand(0, $max)];
		}

		if ($type === 'alnum' AND $length > 1)
		{
			if (\ctype_alpha($str))
			{
				$str[\mt_rand(0, $length - 1)] = \chr(\mt_rand(48, 57));
			}
			elseif (\ctype_digit($str))
			{
				$str[\mt_rand(0, $length - 1)] = \chr(\mt_rand(65, 90));
			}
		}

		return $str;
	}

	public static function censor($str, $badwords, $replacement = '#', $replace_partial_words = TRUE)
	{
		foreach ( (array) $badwords as $key => $badword)
		{
			$badwords[$key] = \str_replace('\*', '\S*?', \preg_quote( (string) $badword));
		}

		$regex = '('.\implode('|', $badwords).')';

		if ($replace_partial_words === FALSE)
		{
			// Just using \b isn't sufficient when we need to replace a badword that already contains word boundaries itself
			$regex = '(?<=\b|\s|^)'.$regex.'(?=\b|\s|$)';
		}

		$regex = '!'.$regex.'!ui';

		if (\Core\UTF8::strlen($replacement) == 1)
		{
			$regex .= 'e';
			return \preg_replace($regex, '\str_repeat($replacement, UTF8::strlen(\'$1\'))', $str);
		}

		return preg_replace($regex, $replacement, $str);
	}

	public static function similar(array $words)
	{
		$word = \current($words);

		for ($i = 0, $max = \strlen($word); $i < $max; ++$i)
		{
			foreach ($words as $w)
			{
				if ( ! isset($w[$i]) OR $w[$i] !== $word[$i])
					break 2;
			}
		}

		return \substr($word, 0, $i);
	}


	public static function number($number)
	{

		$number = (int) $number;
		$text = array();
		$last_unit = NULL;
		$last_item = '';

		foreach (Text::$units as $unit => $name)
		{
			if ($number / $unit >= 1)
			{
				$number -= $unit * ($value = (int) \floor($number / $unit));
				$item = '';

				if ($unit < 100)
				{
					if ($last_unit < 100 AND $last_unit >= 20)
					{
						$last_item .= '-'.$name;
					}
					else
					{
						$item = $name;
					}
				}
				else
				{
					$item = Text::number($value).' '.$name;
				}

				if (empty($item))
				{
					\array_pop($text);

					$item = $last_item;
				}

				$last_item = $text[] = $item;
				$last_unit = $unit;
			}
		}

		if (\count($text) > 1)
		{
			$and = \array_pop($text);
		}

		$text = \implode(', ', $text);

		if (isset($and))
		{
			$text .= ' and '.$and;
		}

		return $text;
	}


	public static function widont($str)
	{
		$str = \rtrim($str);
		$space = \strrpos($str, ' ');

		if ($space !== FALSE)
		{
			$str = \substr($str, 0, $space).'&nbsp;'.\substr($str, $space + 1);
		}

		return $str;
	}

}
