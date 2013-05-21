<?php

require SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Autoloader.php';
class_alias('Hanariu\\Core\\Autoloader', 'Autoloader');

setup_autoloader();

function setup_autoloader()
{
	\Autoloader::add_namespace('Hanariu\\Core', SYSPATH.'classes'); //.'classes/');

	\Autoloader::add_classes(array(
		'Hanariu\\Core\\Arr'           => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Arr.php',
		'Hanariu\\Core\\Config'             => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Config.php',
		'Hanariu\\Core\\Controller'           => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Controller.php',
		'Hanariu\\Core\\Cookie'                     => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Cookie.php',
		'Hanariu\\Core\\Debug'    => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Debug.php',
		'Hanariu\\Core\\Hanariu'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Hanariu.php',
		'Hanariu\\Core\\HTTP'        => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP.php',
		'Hanariu\\Core\\Log'  => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Log.php',
		'Hanariu\\Core\\Profiler'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Profiler.php',
		'Hanariu\\Core\\Request'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Request.php',
		'Hanariu\\Core\\Response'         => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Response.php',
		'Hanariu\\Core\\Route'        => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Route.php',
		'Hanariu\\Core\\URL'   => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'URL.php',
		'Hanariu\\Core\\Utils'       => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Utils.php',

		'Hanariu\\Core\\Config_Group'               => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'Group.php',
		'Hanariu\\Core\\Core_Handler'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'Handler.php',
		'Hanariu\\Core\\Core_Exception'     => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'Exception.php',
		'Hanariu\\Core\\HTTP_Exception'          => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception.php',
		'Hanariu\\Core\\HTTP_Header'           => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Header.php',
		'Hanariu\\Core\\HTTP_Message'          => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Message.php',
		'Hanariu\\Core\\HTTP_Request'     => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Request.php',
		'Hanariu\\Core\\HTTP_Response'           => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Response.php',

		'Hanariu\\Core\\Log_File'           => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.'File.php',
		'Hanariu\\Core\\Log_StdErr'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.'StdErr.php',
		'Hanariu\\Core\\Log_StdOut'  => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.'StdOut.php',
		'Hanariu\\Core\\Log_Syslog'    => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.'Syslog.php',
		'Hanariu\\Core\\Log_Syslog'    => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.'Syslog.php',
		'Hanariu\\Core\\Log_Writer'               => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.'Writer.php',

		'Hanariu\\Core\\Request_Client'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Request'.DIRECTORY_SEPARATOR.'Client.php',
		'Hanariu\\Core\\Request_Client_Curl'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Request'.DIRECTORY_SEPARATOR.'Client'.DIRECTORY_SEPARATOR.'Curl.php',
		'Hanariu\\Core\\Request_Client_External'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Request'.DIRECTORY_SEPARATOR.'Client'.DIRECTORY_SEPARATOR.'External.php',
		'Hanariu\\Core\\Request_Client_HTTP'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Request'.DIRECTORY_SEPARATOR.'Client'.DIRECTORY_SEPARATOR.'HTTP.php',
		'Hanariu\\Core\\Request_Client_Internal'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Request'.DIRECTORY_SEPARATOR.'Client'.DIRECTORY_SEPARATOR.'Internal.php',
		'Hanariu\\Core\\Request_Client_Stream'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Request'.DIRECTORY_SEPARATOR.'Client'.DIRECTORY_SEPARATOR.'Stream.php',
		'Hanariu\\Core\\Request_Client_Recursion_Exception'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'Request'.DIRECTORY_SEPARATOR.'Client'.DIRECTORY_SEPARATOR.'Recursion'.DIRECTORY_SEPARATOR.'Exception.php',
		
		'Hanariu\\Core\\HTTP_Exception_E301'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E301.php',
		'Hanariu\\Core\\HTTP_Exception_E302'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E302.php',
		'Hanariu\\Core\\HTTP_Exception_E303'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E303.php',
		'Hanariu\\Core\\HTTP_Exception_E304'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E304.php',
		'Hanariu\\Core\\HTTP_Exception_E305'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E305.php',
		'Hanariu\\Core\\HTTP_Exception_E307'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E307.php',
		'Hanariu\\Core\\HTTP_Exception_E400'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E400.php',
		'Hanariu\\Core\\HTTP_Exception_E401'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E401.php',
		'Hanariu\\Core\\HTTP_Exception_E402'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E402.php',
		'Hanariu\\Core\\HTTP_Exception_E403'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E403.php',
		'Hanariu\\Core\\HTTP_Exception_E404'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E404.php',
		'Hanariu\\Core\\HTTP_Exception_E405'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E405.php',
		'Hanariu\\Core\\HTTP_Exception_E406'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E406.php',
		'Hanariu\\Core\\HTTP_Exception_E407'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E407.php',
		'Hanariu\\Core\\HTTP_Exception_E408'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E408.php',
		'Hanariu\\Core\\HTTP_Exception_E409'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E409.php',
		'Hanariu\\Core\\HTTP_Exception_E410'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E410.php',
		'Hanariu\\Core\\HTTP_Exception_E411'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E411.php',
		'Hanariu\\Core\\HTTP_Exception_E412'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E412.php',
		'Hanariu\\Core\\HTTP_Exception_E413'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E413.php',
		'Hanariu\\Core\\HTTP_Exception_E414'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E414.php',
		'Hanariu\\Core\\HTTP_Exception_E415'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E415.php',
		'Hanariu\\Core\\HTTP_Exception_E416'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E416.php',
		'Hanariu\\Core\\HTTP_Exception_E417'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E417.php',
		'Hanariu\\Core\\HTTP_Exception_E500'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E500.php',
		'Hanariu\\Core\\HTTP_Exception_E501'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E501.php',
		'Hanariu\\Core\\HTTP_Exception_E502'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E502.php',
		'Hanariu\\Core\\HTTP_Exception_E503'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E503.php',
		'Hanariu\\Core\\HTTP_Exception_E504'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E504.php',
		'Hanariu\\Core\\HTTP_Exception_E505'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'E505.php',
		
		'Hanariu\\Core\\HTTP_Exception_Expected'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'Expected.php',
		'Hanariu\\Core\\HTTP_Exception_Redirect'      => SYSPATH.'classes'.DIRECTORY_SEPARATOR.'HTTP'.DIRECTORY_SEPARATOR.'Exception'.DIRECTORY_SEPARATOR.'Redirect.php',
	));
};
