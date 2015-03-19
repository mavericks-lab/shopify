<?php
use Maverickslab\Shopify\Shopify;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/16/15
 * Time: 11:12 PM
 */

class ShopifyTest extends PHPUnit_Framework_TestCase {

    public function setUp(){
        $this->mockedRequestor = Mockery::mock('Maverickslab\Shopify\ApiRequestor');
        $this->shopify = new Shopify($this->mockedRequestor);
    }

    public function test_overloading_works(){
        //$this->assertEquals('posts', $this->shopify->posts());
    }


    public function test_sanitizes_class_name(){
        $className = $this->shopify->sanitizeClassName('products');
        $this->assertEquals('Product', $className);
    }

    public function test_it_resolves_into_a_resource_object(){
        $product = $this->shopify->shop('store_url', 'store_token')->products();
        $this->assertInstanceOf('Maverickslab\Shopify\Resources\Product', $product);
    }

//    /**
//     * @expectedException ShopifyException:
//     */
//    public function test_throws_an_exception_if_a_store_url_is_not_provided(){
//        $product = $this->shopify->shop()->products();
//    }


    public function test_connects_a_shopify_shop(){
        $this->mockedRequestor->shouldReceive('install')->once()->andReturn('installed');

        $result = $this->shopify->shop('store_url')->install();

        $this->assertEquals('installed', $result);
    }

}
 