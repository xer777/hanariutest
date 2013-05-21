<?php 

//\Autoloader::add_namespace('Core', __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'');
//Autoloader::add_core_namespace('Core', true);

\Autoloader::add_classes(array(
	//'Core_Exception'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Core/Exception.php',
	//system replace
	'URL'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'URL.php',
	'Debug'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Debug.php',
	'Controller'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Controller.php',
	//rest
	'Core\\View'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'View.php',
	'Core\\Controller_Template'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.'Template.php',
	'Core\\Controller_Restler'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.'Restler.php',
	'Core\\Controller_Restful'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.'Restful.php',
	'Core\\UTF8'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8.php',
	'Core\\Upload'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Upload.php',
	'Core\\Text'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Text.php',
	'Core\\Session'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Session.php',
	'Core\\Security'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Security.php',
	'Core\\Num'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Num.php',
	'Core\\Model'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Model.php',
	'Core\\Message'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Message.php',
	'Core\\I18n'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'I18n.php',
	'Core\\HTML'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'HTML.php',
	'Core\\Form'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Form.php',
	'Core\\File'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'File.php',
	'Core\\Encrypt'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Encrypt.php',
	'Core\\Date'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Date.php',
	'Core\\Validation_Exception'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Validation/Exception.php',
	'Core\\UTF8_Exception'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Exception.php',
	'Core\\Session_Cookie'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Session'.DIRECTORY_SEPARATOR.'Cookie.php',
	'Core\\Session_Exception'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Session'.DIRECTORY_SEPARATOR.'Exception.php',
	'Core\\Session_Native'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Session/Native.php',
	'Core\\UTF8_FromUnicode'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'FromUnicode.php',
	'Core\\UTF8_Ltrim'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Ltrim.php',
	'Core\\UTF8_Ord'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Ord.php',
	'Core\\UTF8_Rtrim'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Rtrim.php',
	'Core\\UTF8_Strcasecmp'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strcasecmp.php',
	'Core\\UTF8_Strcspn'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strcspn.php',
	'Core\\UTF8_StrIreplace'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'StrIreplace.php',
	'Core\\UTF8_Stristr'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Stristr.php',
	'Core\\UTF8_Strlen'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strlen.php',
	'Core\\UTF8_StrPad'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'StrPad.php',
	'Core\\UTF8_Strpos'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strpos.php',
	'Core\\UTF8_Strrev'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strrev.php',
	'Core\\UTF8_Strrpos'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strrpos.php',
	'Core\\UTF8_StrSplit'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'StrSplit.php',
	'Core\\UTF8_Strspn'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strspn.php',
	'Core\\UTF8_Strtolower'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strtolower.php',
	'Core\\UTF8_Strtoupper'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Strtoupper.php',
	'Core\\UTF8_Substr'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Substr.php',
	'Core\\UTF8_FSubstrReplace'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'SubstrReplace.php',
	'Core\\UTF8_ToUnicode'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'ToUnicode.php',
	'Core\\UTF8_TransliterateToAscii'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'TransliterateToAscii.php',
	'Core\\UTF8_Trim'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Trim.php',
	'Core\\UTF8_Ucfirst'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Ucfirst.php',
	'Core\\UTF8_Ucwords'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'UTF8'.DIRECTORY_SEPARATOR.'Ucwords.php',
));

if ( ! \function_exists('__'))
{
	function __($string, array $values = NULL, $lang = 'en')
	{
		if ($lang !== \Core\I18n::$lang)
		{
			$string = \Core\I18n::get($string);
		}

		return empty($values) ? $string : \strtr($string, $values);
	}
}