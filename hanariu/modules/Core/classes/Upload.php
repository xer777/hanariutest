<?php namespace Core;

class Upload {

	public static $remove_spaces = TRUE;
	public static $default_directory = 'upload';


	public static function save(array $file, $filename = NULL, $directory = NULL, $chmod = 0644)
	{
		if ( ! isset($file['tmp_name']) OR ! \is_uploaded_file($file['tmp_name']))
		{
			return FALSE;
		}

		if ($filename === NULL)
		{
			$filename = \uniqid().$file['name'];
		}

		if (\Core\Upload::$remove_spaces === TRUE)
		{
			$filename = \preg_replace('/\s+/u', '_', $filename);
		}

		if ($directory === NULL)
		{
			$directory = \Core\Upload::$default_directory;
		}

		if ( ! \is_dir($directory) OR ! \is_writable(\realpath($directory)))
		{
			throw new \Core_Exception('Directory :dir must be writable',
				array(':dir' => \Debug::path($directory)));
		}

		$filename = \realpath($directory).DIRECTORY_SEPARATOR.$filename;

		if (\move_uploaded_file($file['tmp_name'], $filename))
		{
			if ($chmod !== FALSE)
			{
				\chmod($filename, $chmod);
			}

			// Return new file path
			return $filename;
		}

		return FALSE;
	}

	public static function valid($file)
	{
		return (isset($file['error'])
			AND isset($file['name'])
			AND isset($file['type'])
			AND isset($file['tmp_name'])
			AND isset($file['size']));
	}

	public static function not_empty(array $file)
	{
		return (isset($file['error'])
			AND isset($file['tmp_name'])
			AND $file['error'] === UPLOAD_ERR_OK
			AND \is_uploaded_file($file['tmp_name']));
	}

	public static function type(array $file, array $allowed)
	{
		if ($file['error'] !== UPLOAD_ERR_OK)
			return TRUE;

		$ext = \strtolower(\pathinfo($file['name'], PATHINFO_EXTENSION));

		return \in_array($ext, $allowed);
	}

	public static function size(array $file, $size)
	{
		if ($file['error'] === UPLOAD_ERR_INI_SIZE)
		{
			return FALSE;
		}

		if ($file['error'] !== UPLOAD_ERR_OK)
		{
			return TRUE;
		}

		$size = \Core\Num::bytes($size);
		return ($file['size'] <= $size);
	}

	public static function image(array $file, $max_width = NULL, $max_height = NULL, $exact = FALSE)
	{
		if (\Core\Upload::not_empty($file))
		{
			try
			{
				list($width, $height) = \getimagesize($file['tmp_name']);
			}
			catch (\ErrorException $e)
			{

			}

			if (empty($width) OR empty($height))
			{
				return FALSE;
			}

			if ( ! $max_width)
			{
				$max_width = $width;
			}

			if ( ! $max_height)
			{
				$max_height = $height;
			}

			if ($exact)
			{
				return ($width === $max_width AND $height === $max_height);
			}
			else
			{
				return ($width <= $max_width AND $height <= $max_height);
			}
		}

		return FALSE;
	}

}
