<?php 

require SYSPATH.'bootstrap.php';

\Autoloader::add_classes(array(
	'Core_Exception'             => __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'Exception.php', //if you want exceptions based on View
));

Autoloader::register();


\date_default_timezone_set('Europe/Warsaw');
\setlocale(LC_ALL, 'pl_PL.utf-8');
\ini_set('unserialize_callback_func', 'spl_autoload_call');

if (isset($_SERVER['HANARIU_ENV']))
{
	Hanariu::$environment = \constant('Hanariu::'.\strtoupper($_SERVER['HANARIU_ENV']));
}

/**
 * Initialize Hanariu, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */

Hanariu::init(array(
	'base_url'   => '/fuelhana/',
	'index_file'   => FALSE,
    'errors' => TRUE,
    'profile' => FALSE,
    'caching' => FALSE,
));

Hanariu::$log->attach(new \Log_File(APPPATH.'logs'));

Hanariu::modules(array(
	//'cache'      => MODPATH.'cache',  
	//'rest'      => MODPATH.'rest',
	 //'cabinet'      => MODPATH.'cabinet',
	 //'carbon'      => MODPATH.'carbon',
	 //'expressive'      => MODPATH.'expressive',
	 //'respect'      => MODPATH.'respect',
	 //'aura'      => MODPATH.'aura', 
	 //'faker'      => MODPATH.'faker',
	'Core'      => MODPATH.'Core',
	//'devtools'      => MODPATH.'devtools',
	));

\Core\I18n::lang('pl-pl');

\Hanariu::$profiling = TRUE;


/*\Route::set('rest', 'rest(/<par1>(/<par2>(/<par3>(/<par4>(/<par5>)))))(.<format>)')
 	->defaults(array(
 		'controller' => 'rest',
 		'action'     => 'index',
 		'format'     => 'jsonp',
 	));
*/
\Route::set('default', '(<controller>(/<action>(/<sub>)))')
	->defaults(array(
		'controller' => 'index',
		'action'     => 'index',
		'sub'     => 'index',
	));

\Cookie::$salt = '123abc';
\Cookie::$expiration = '180000';
