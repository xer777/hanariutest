<?php

\define('EXT', '.php');

//\error_reporting(E_ALL | E_STRICT);
\error_reporting(E_ALL);
\ini_set('display_errors', '1');

define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);
define('APPPATH', realpath(__DIR__.'/hanariu/app/').DIRECTORY_SEPARATOR);
define('MODPATH', realpath(__DIR__.'/hanariu/modules/').DIRECTORY_SEPARATOR);
define('SYSPATH', realpath(__DIR__.'/hanariu/core/').DIRECTORY_SEPARATOR);

// Get the start time and memory for use later
defined('HANARIU_START_TIME') or define('HANARIU_START_TIME', microtime(true));
defined('HANARIU_START_MEMORY') or define('HANARIU_START_MEMORY', memory_get_usage());

if (\file_exists('install'.EXT))
{
	return include 'install'.EXT;
}

require APPPATH.'bootstrap'.EXT;

/*echo Request::factory(TRUE,FALSE,TRUE)
		->execute()
		->send_headers(TRUE)
		->body();
*/
$request = \Request::factory()->execute(); 

if (! \defined('SUPPRESS_REQUEST') AND $request->body())
{
	// Get the total memory and execution time
	$total = array(
		'{memory_usage}'   => \number_format((\memory_get_peak_usage() - HANARIU_START_MEMORY) / 1024, 2).'KB',
		'{execution_time}' => \number_format(\microtime(TRUE) - HANARIU_START_TIME, 5).'s');

	// Insert the totals into the response
	$total_stats = \strtr((string) $request->body(), $total); 
	$request->body($total_stats);
}

if ( ! \defined('SUPPRESS_REQUEST'))
{

	echo $request->send_headers()->body();		
}
