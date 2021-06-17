<?php
//mb_internal_encoding("UTF-8");
set_time_limit(0);
error_reporting(E_ALL ^ E_NOTICE);

define('ROOTDIR', dirname(__DIR__));
define('DATADIR', ROOTDIR . '/data');

//===== settings hidden ========================================================
define('VERBOSE', true);
define('VERBOSE_CURL', false);
define('VERBOSE_CURL_PARAM', false);

foreach (scandir(__DIR__) as $f)
	if (!preg_match('~\.$|_|loader~si', $f) && !is_dir(__DIR__ . "/{$f}"))
		require_once(__DIR__ . "/{$f}");

	
set_exception_handler('Exception_handler');

function Exception_handler($e) 
{
	echo "##Uncaught exception## " . date("Ymd-His") . "\n";
	var_dump($e);
}
	