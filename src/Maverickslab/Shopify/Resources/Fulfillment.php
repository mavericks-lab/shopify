<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/11/15
 * Time: 4:19 PM
 */

namespace Maverickslab\Shopify\Resources;


use Maverickslab\Shopify\ApiRequestor;
use Maverickslab\Shopify\Exceptions\ShopifyException;

class Fulfillment {

    private $requestor;

    public function __construct( ApiRequestor $requestor){
        $this->requestor = $requestor;
        $this->requestor->resource = '/admin/orders';
    }

    public function create ( $order_id, $post_data )
    {
        $this->requestor->resource = $this->requestor->resource.'/'.$order_id.'/fulfillments';
        if(sizeof($post_data) < 0)
            throw new ShopifyException('Fulfillment Data is empty');

        return $this->requestor->post( $post_data );
    }

} 