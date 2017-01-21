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
	protected $_encoding;
	protected $_client;
	protected $_session;

	/**
	 * @param string $configFile
	 */
	public function __construct(\Zend\Config\Config $config)
	{
		$this->_encoding = strtolower($config->encoding);

		switch ( $this->_encoding ) {
			case 'soap':
			$this->_client = new SoapClient($config->host . '/api/soap/?wsdl');
			$this->_session = $this->_client->login($config->user, $config->key);
			break;

			case 'xml-rpc':
			case 'xmlrpc':
			$this->_encoding = 'xmlrpc';
			$this->_client = new Zend\XmlRpc\Client($config->host . '/api/xmlrpc');
// 			print_r($this->_client->getIntrospector()->listMethods());
// 			$this->_session = $this->_client->call('login', [$config->user, $config->key]);
			break;

			default:
			throw new UnexpectedValueException('unknown connection method: ' . $config->method);
		}
	}

	public function __destruct()
	{
		if ( $this->_encoding === 'soap' ) {
			$this->_client->endSession($this->_session);
		}
		else {
			;
		}
	}

	/**
	 * @return SoapClient|Zend\XmlRpc\Client
	 */
	function getClient()
	{
		return $this->_client;
	}

	/**
	 * @return string
	 */
	function getSession()
	{
		return $this->_session;
	}

	/**
	 * @param string $method
	 * @param scalar|array $args -OPTIONAL
	 * @return mixed
	 */
	function __call($method, $args=null)
	{
		$this->call($method, $args);
	}

	/**
	 * Magento methods, or messages, will have a period in their name
	 * so it must be passed as a string.
	 *
	 * @param string $method
	 * @param scalar|array $args -OPTIONAL
	 * @return mixed
	 */
	function call($method, $args=null)
	{
		if ( $this->_encoding === 'soap' ) {
			if ( $args === null ) {
				return $this->_client->call($this->_session, $method);
			}
			elseif ( is_scalar($args) ) {
				return $this->_client->call($this->_session, $method, [$args]);
			}
			elseif ( is_array($args) ) {
				return $this->_client->call($this->_session, $method, $args);
			}
		}
		else {
			;
		}

		throw new LogicException('bad type for "args"');
	}
}
