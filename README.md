## Magento 1 SOAP API testing
Magento WEB API testing framework. Runs user added PHP files in "./tests/" in alphabetical order.

This utility requires a PHP 5.5 or higher, and [Composer](https://getcomposer.org).

## A better idea.
It's probably better to not use the above code. It could best be used as an example of over-coding. ;-)

For basic API testing, or as a starting point for your own code use one of these examples:
```
//  SOAP version 1:
$client = new SoapClient('https://your.store.com/api/soap/?wsdl=1');
$session = $client->login('uname', 'key');
echo $client->call($session, 'product_stock.update', ['SP152', ['qty'=>21, 'is_in_stock'=>1]]);
$client->endSession($session);
//  prints the character "1"

//  SOAP version 2:
$client = new SoapClient('https://your.store.com/api/v2_soap/?wsdl=1');
$session = $client->login('uname', 'key');
echo $client->cataloginventoryStockItemUpdate($session, 'SP152', ['qty'=>21, 'is_in_stock'=>1]);
$client->endSession($session);
//  prints the character "1"
```
For me, the need of both client and session variables begs for this to be wrapped in a class implementation.

### SOAP v1 and v2
It's important to note that Magento 1's two SOAP versions have different ways of calling API methods. In v1 the method name is passed as a parameter of type string to a method named “call”, and in v2 it is called directly as a SoapClient method.

Notice that the method name “product_stock.update” is actually a shorter alias for the full name “cataloginventory_stock_item.update”. The difference between v1 and v2 usage is to remove the underscores and periods from the name and capitalize the letters just after the removed punctuation. Also note the way parameters are passed to the two versions. V1 has another set of array square brackets for passing parameters.

See the [Magento 1 Documentation](http://devdocs.magento.com/guides/m1x/api/soap/introduction.html) for more information including available methods. Use this code, in either SOAP version, to retrieve a structured listing of Magento 1 API methods available to the current session:
```
print_r( $client->resources($session) );
```
