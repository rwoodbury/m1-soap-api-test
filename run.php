#!/usr/bin/env php
<?php

function pout($s)
{
	fwrite( STDOUT, $s );
}

function perr($s)
{
	fwrite( STDERR, $s );
}

/**
 * Convert all errors into ErrorExceptions
 */
set_error_handler(
	function ($severity, $errstr, $errfile, $errline) {
		throw new ErrorException($errstr, 1, $severity, $errfile, $errline);
	},
	E_USER_ERROR
);

/**
 * Set handler for uncaught exceptions.
 */
set_exception_handler(
	function ( Exception $e ) {
// 		perr( $e->getMessage() . PHP_EOL );
		perr( $e . PHP_EOL );
		exit( $e->getCode() );
	}
);

require_once __DIR__ . '/vendor/autoload.php';

//	This keeps the API connection open until the script is finnished.
//	The static "ApiClient::call" can then be used anywhere without a global declaration.
require 'ApiClient.php';
$_clientSessionHandler = new ApiClient(__DIR__ . '/config.php');

$filesToIgnore = [
	'ApiClient.php',
	'config.example.php',
	'config.php',
	'run.php'
];

foreach ( glob(__DIR__ . '/*.php', GLOB_MARK) as $f ) {
	if ( substr($f, -1) !== '/' && !in_array( basename($f), $filesToIgnore) ) {
		include $f;
	}
}
