<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/17/15
 * Time: 12:31 AM
 */

namespace Maverickslab\Shopify\Resources;


use Maverickslab\Shopify\ApiRequestor;

class Product implements ResourceInterface{

    /**
     * @var ApiRequestor
     */
    private $requestor;

    public function __construct( ApiRequestor $requestor){
        $this->requestor = $requestor;
        $this->requestor->resource = '/admin/products';
    }


    public function get ( $id = null, $options = [] )
    {
        return $this->requestor->get($id, $options);
    }

    public function create ()
    {
        // TODO: Implement create() method.
    }
}