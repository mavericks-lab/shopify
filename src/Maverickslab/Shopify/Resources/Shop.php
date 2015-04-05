<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/5/15
 * Time: 5:04 AM
 */

namespace Maverickslab\Shopify\Resources;


use Maverickslab\Shopify\ApiRequestor;

class Shop extends BaseResource{

    /**
     * @var ApiRequestor
     */
    protected  $requestor;

    public function __construct(ApiRequestor $requestor){

        $this->requestor = $requestor;
        $this->requestor->resource = '/admin/shop';
    }
} 