<?php namespace Core;

class File {

	public static function mime($filename)
	{
		$filename = \realpath($filename);
		$extension = \strtolower(\pathinfo($filename, PATHINFO_EXTENSION));

		if (\preg_match('/^(?:jpe?g|png|[gt]if|bmp|swf)$/', $extension))
		{
			$file = \getimagesize($filename);

			if (isset($file['mime']))
				return $file['mime'];
		}

		if (\class_exists('finfo', FALSE))
		{
			if ($info = new \finfo(\defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME))
			{
				return $info->file($filename);
			}
		}

		if (\ini_get('mime_magic.magicfile') AND \function_exists('mime_content_type'))
		{
			return \mime_content_type($filename);
		}

		if ( ! empty($extension))
		{
			return \Core\File::mime_by_ext($extension);
		}

		return FALSE;
	}


	public static function mime_by_ext($extension)
	{
		$mimes = \Hanariu::$config->load('mimes');

		return isset($mimes[$extension]) ? $mimes[$extension][0] : FALSE;
	}


	public static function mimes_by_ext($extension)
	{
		$mimes = \Hanariu::$config->load('mimes');

		return isset($mimes[$extension]) ? ( (array) $mimes[$extension]) : array();
	}


	public static function exts_by_mime($type)
	{
		static $types = array();

		if (empty($types))
		{
			foreach (\Hanariu::$config->load('mimes') as $ext => $mimes)
			{
				foreach ($mimes as $mime)
				{
					if ($mime == 'application/octet-stream')
					{
						continue;
					}

					if ( ! isset($types[$mime]))
					{
						$types[$mime] = array( (string) $ext);
					}
					elseif ( ! \in_array($ext, $types[$mime]))
					{
						$types[$mime][] = (string) $ext;
					}
				}
			}
		}

		return isset($types[$type]) ? $types[$type] : FALSE;
	}

	public static function ext_by_mime($type)
	{
		return \current(\Core\File::exts_by_mime($type));
	}

	public static function split($filename, $piece_size = 10)
	{
		$file = \fopen($filename, 'rb');
		$piece_size = \floor($piece_size * 1024 * 1024);
		$block_size = 1024 * 8;
		$peices = 0;

		while ( ! \feof($file))
		{
			$peices += 1;
			$piece = \str_pad($peices, 3, '0', STR_PAD_LEFT);
			$piece = \fopen($filename.'.'.$piece, 'wb+');
			$read = 0;

			do
			{
				\fwrite($piece, \fread($file, $block_size));
				$read += $block_size;
			}
			while ($read < $piece_size);
			\fclose($piece);
		}

		\fclose($file);
		return $peices;
	}

	public static function join($filename)
	{
		$file = \fopen($filename, 'wb+');
		$block_size = 1024 * 8;
		$pieces = 0;

		while (\is_file($piece = $filename.'.'.\str_pad($pieces + 1, 3, '0', STR_PAD_LEFT)))
		{
			$pieces += 1;
			$piece = \fopen($piece, 'rb');

			while ( ! \feof($piece))
			{
				\fwrite($file, \fread($piece, $block_size));
			}

			\fclose($piece);
		}

		return $pieces;
	}

}
