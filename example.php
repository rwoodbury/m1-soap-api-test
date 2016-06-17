<?php

//	Print a list of available API method calls.
$res = ApiClient::resources(ApiClient::getSession());
// print_r($res);
foreach ( $res as $r ) {
	foreach ( $r['methods'] as $m ) {
		pout($m['path'] . PHP_EOL);
	}
}
