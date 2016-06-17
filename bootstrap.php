<?php

/**
 * Convert all errors into ErrorExceptions
 */
// set_error_handler(
// 	function ($severity, $errstr, $errfile, $errline) {
// 		throw new ErrorException($errstr, 1, $severity, $errfile, $errline);
// 	},
// 	E_USER_ERROR
// );

/**
 * Set handler for uncaught exceptions.
 */
set_exception_handler(
	function ( Exception $e ) {
// 		fwrite( STDERR, $e->getMessage() . PHP_EOL );
		fwrite( STDERR, $e . PHP_EOL );
		exit( $e->getCode() );
	}
);

require_once __DIR__ . '/vendor/autoload.php';

//	This keeps the API connection open until the script is finnished.
//	The static "ApiClient::call" can then be used anywhere without a global declaration.
require 'ApiClient.php';
$_clientSessionHandler = new ApiClient;
