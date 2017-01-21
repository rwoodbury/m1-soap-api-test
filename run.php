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

require_once __DIR__ . '/vendor/autoload.php';

try {
	$config = \Zend\Config\Factory::fromFile(__DIR__ . '/config.php', true);
}
catch ( Exception $e ) {
	perr( $e->__toString() . "\n" );
	perr( "Copy 'config.example.php' to 'config.php' then add your settings.\n" );
}

try {
	//	This keeps the API connection open until the script is finnished.
	//	The static "ApiClient::call" can then be used anywhere without a global declaration.
	require __DIR__ . '/ApiClient.php';
	$apiClient = new ApiClient($config);

	foreach ( glob(__DIR__ . '/tests/*.php', GLOB_MARK) as $f ) {
		if ( substr($f, -1) !== '/') {
			include $f;
		}
	}
}
catch ( Exception $e ) {
	perr( $e . PHP_EOL );
	exit( $e->getCode() );
}

unset($apiClient);
pout(PHP_EOL);
