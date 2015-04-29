<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/18/15
 * Time: 3:01 PM
 */

namespace Maverickslab\Shopify\Exceptions;


use Exception;

class ShopifyException extends Exception{

    /**
     *
     * @var string
     */
    protected $errors;

    public function __construct($message, $errors=[], $code = 0, Exception $previous = null){
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return all errors on the validator
     */
    public function getErrors(){
        return $this->errors;
    }
} 