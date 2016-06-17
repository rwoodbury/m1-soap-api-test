<?php

class ApiClient
{
	protected static $_method;
	protected static $_client;
	protected static $_session;

	public function __construct()
	{
		if ( isset(self::$_client) ) {
			throw new LogicException('Connection already created to deployment instance.');
		}

		try {
			$config = Zend\Config\Factory::fromFile('config.php', true);
		}
		catch ( Exception $e ) {
			echo $e->__toString(), "\n";
			echo "Copy 'config.example.php' to 'config.php' then add your settings.\n";
		}

		self::$_method = strtolower($config->method);

		switch ( self::$_method ) {
			case 'soap':
			self::$_client = new Zend\Soap\Client($config->host . '/api/?wsdl');
			self::$_session = self::$_client->login($config->user, $config->key);
			break;

// 			case 'xml-rpc':
// 			case 'xmlrpc':
// 			self::$_method = 'xmlrpc';
// 			self::$_client = new Zend\XmlRpc\Client($config->host . '/api/xmlrpc');
// 			self::$_session = self::$_client->call('login', [$config->user, $config->key]);
// 			break;

			default:
			throw new UnexpectedValueException('unknown connection method: ' . $config->method);
		}
	}

	public function __destruct()
	{
		self::$_client->endSession($session);
	}

	/**
	 * @param string $method
	 * @param scalar|array $args -OPTIONAL
	 */
	static function call($method, $args=null)
	{
		if ( self::$_method === 'soap' ) {
			if ( $args === null ) {
				return self::$_client->call(self::$_session, $method);
			}
			elseif ( is_array($args) ) {
				return self::$_client->call(self::$_session, $method, $args);
			}
		}
// 		else {
// 			;
// 		}

		throw new LogicException('bad type for "args"');
	}
}
