<?php

class ExampleTest extends PHPUnit\Framework\TestCase
{
	function testExample()
	{
		$this->expectOutputString('');
		echo 'Tests should be in the same directory as "tests/Example.php"';
	}
}
