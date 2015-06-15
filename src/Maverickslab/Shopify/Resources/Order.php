<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/17/15
 * Time: 12:31 AM
 */

namespace Maverickslab\Shopify\Resources;


use Maverickslab\Shopify\ApiRequestor;
use Maverickslab\Shopify\Exceptions\ShopifyException;

class Order extends BaseResource{


    public function __construct( ApiRequestor $requestor){
        $this->requestor = $requestor;
        $this->requestor->resource = '/admin/orders';
    }


//    public function get ( $id = null, $options = [] )
//    {
//        return $this->requestor->get($id, $options);
//    }
//
//    public function create ( $post_data )
//    {
//        if(sizeof($post_data) < 0)
//            throw new ShopifyException('Create Data is empty');
//
//        return $this->requestor->post( $post_data );
//    }
//
//    public function modify ( $id, $modify_data )
//    {
//        if(is_null($id))
//            throw new ShopifyException('Order Id not provided');
//
//        if(sizeof($modify_data) < 0)
//            throw new ShopifyException('Modify Data is empty');
//
//        return $this->requestor->put($id, $modify_data);
//    }
//
//    public function remove ( $id )
//    {
//        if(is_null($id))
//            throw new ShopifyException('Order Id not provided');
//
//        return $this->requestor->delete( $id );
//    }
}