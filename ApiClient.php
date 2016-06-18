<?php

/**
 * This class simplifies the setup and usage of the Magento 1 WEB API. It
 *		enforces that there be only one connection available for the run
 *		of the test script.
 *
 * For example, where the Magento manual defines a call like this:
 *		$client = new SoapClient('http://magentohost/api/soap/?wsdl');
 *		$session = $client->login('apiUser', 'apiKey');
 * 		$result = $client->call($session, 'catalog_product.info', '4');
 *
 * the usage in the user's scripts will be the more straight forward:
 *		$result = ApiClient::call('catalog_product.info', '4');
 *
 * Even support methods like:
 *		$result = $client->resources($session);
 *
 * will be called with:
 *		$result = ApiClient::resources(ApiClient::getSession());
 */
class ApiClient
{
	protected static $_encoding;
	protected static $_client;
	protected static $_session;

	/**
	 * @param string $configFile
	 */
	public function __construct($configFile)
	{
		if ( isset(self::$_client) ) {
			throw new LogicException('Connection already created to deployment instance.');
		}

		try {
			$config = Zend\Config\Factory::fromFile($configFile, true);
		}
		catch ( Exception $e ) {
			perr( $e->__toString() . "\n" );
			perr( "Copy 'config.example.php' to 'config.php' then add your settings.\n" );
		}

		self::$_encoding = strtolower($config->encoding);

		switch ( self::$_encoding ) {
			case 'soap':
// 			self::$_client = new Zend\Soap\Client($config->host . '/api/?wsdl');
			self::$_client = new SoapClient($config->host . '/api/?wsdl');
			self::$_session = self::$_client->login($config->user, $config->key);
			break;

// 			case 'xml-rpc':
// 			case 'xmlrpc':
// 			self::$_encoding = 'xmlrpc';
// 			self::$_client = new Zend\XmlRpc\Client($config->host . '/api/xmlrpc');
// 			self::$_session = self::$_client->call('login', [$config->user, $config->key]);
// 			break;

			default:
			throw new UnexpectedValueException('unknown connection method: ' . $config->method);
		}
	}

	public function __destruct()
	{
		self::$_client->endSession(self::$_session);
	}

	/**
	 * @param string $method
	 * @param scalar|array $args -OPTIONAL
	 * @return mixed
	 */
	static function call($method, $args=null)
	{
		if ( self::$_encoding === 'soap' ) {
			if ( $args === null ) {
				return self::$_client->call(self::$_session, $method);
			}
			elseif ( is_scalar($args) ) {
				return self::$_client->call(self::$_session, $method, [$args]);
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

	/**
	 * @param string $method
	 * @param array $args -OPTIONAL
	 * @return mixed
	 */
	static function __callStatic($method, $args=null)
	{
		if ( self::$_encoding === 'soap' ) {
			return self::$_client->__soapCall($method, $args);
		}
// 		else {
// 			;
// 		}
	}

	/**
	 * @return string
	 */
	static function getSession()
	{
		return self::$_session;
	}
}
