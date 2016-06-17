<?php

class ExampleTest extends PHPUnit\Framework\TestCase
{
	/**
	 * This test causes a failure outputting the instructional string below.
	 */
	function testExample()
	{
		$this->expectOutputString('');
		echo 'Tests should be in the same directory as "tests/Example.php"';
	}
}
