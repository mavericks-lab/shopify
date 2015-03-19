<?php
use Maverickslab\Shopify\ApiRequestor;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/18/15
 * Time: 7:57 AM
 */

class ApiRequestorTest extends PHPUnit_Framework_TestCase {

    public function setUp(){
        $this->mockedClient = Mockery::mock('Guzzle\Service\Client');
        $this->requestor = new ApiRequestor($this->mockedClient);
    }


    public function test_gets_a_query_string_from_an_array_of_options(){
        $options = ['client_id' => 'awkigowhlnd56kdsl', 'client_secret' => 'tokslkoqaspwselaksd'];

        $queryString = $this->requestor->getQueryString($options);
        $this->assertEquals('?client_id=awkigowhlnd56kdsl&client_secret=tokslkoqaspwselaksd', $queryString);
    }


    public function test_gets_an_empty_string_from_empty_options_array(){
        $options = [];

        $queryString = $this->requestor->getQueryString($options);
        $this->assertEquals('', $queryString);
    }

    public function test_makes_sure_a_url_has_http_prefix(){
        $_url1 = 'myshop.myshopify.com';
        $_url2 = 'yourshop.myshopify.com';

        $url1 = $this->requestor->sanitizeUrl($_url1);
        $url2 = $this->requestor->sanitizeUrl($_url2);

        $this->assertEquals('https://myshop.myshopify.com', $url1);
        $this->assertEquals('https://yourshop.myshopify.com', $url2);
    }


    public function test_appends_json_to_the_end_of_a_url(){
        $_url1 = 'http://mydomain.com/products';
        $_url2 = 'http://mydomain.com/products/234';

        $url1 = $this->requestor->jsonizeUrl($_url1);
        $url2 = $this->requestor->jsonizeUrl($_url2);

        $this->assertEquals('http://mydomain.com/products.json', $url1);
        $this->assertEquals('http://mydomain.com/products/234.json', $url2);
    }


    public function test_appends_resource_id_to_a_url(){
        $_url = 'http://mydomain.com/products';

        $url = $this->requestor->appendResourceId($_url, 12987);

        $this->assertEquals('http://mydomain.com/products/12987', $url);
    }

    public function test_formats_scopes_array(){
        $scopes = ['products' => ['read', 'write'], 'orders' => ['read', 'write']];

        $scope = $this->requestor->generateScope($scopes);

        $this->assertEquals('read_products,write_products,read_orders,write_orders', $scope);
    }


    public function test_gets_client_id(){
        $this->requestor->getClientId();
    }

//    /**
//     * @expectedException Maverickslab\Shopify\Exceptions\ShopifyException:
//     */
//    public function test_throws_exception_if_store_url_is_not_provided(){
//
//        $scope = $this->requestor->install();
//    }


}
 