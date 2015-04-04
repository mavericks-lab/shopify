<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/16/15
 * Time: 10:56 PM
 */

namespace Maverickslab\Shopify;


use Guzzle\Service\Client;
use Illuminate\Support\Facades\Config;
use Maverickslab\Shopify\Exceptions\ShopifyException;

class ApiRequestor {

    /**
     * @var
     */
    private $client;

    private $authorizationUrl = '/admin/oauth/authorize';

    private $tokenUrl = '/admin/oauth/access_token';

    private $url;

    public $storeUrl;

    public $storeToken;

    public  $resource;

    public function __construct(Client $client){
        $this->client = $client;
    }


    public function install(){
        $options = $this->getAuthorizationOptions ();

        $link = $this->sanitizeUrl($this->getStoreUrl()). $this->authorizationUrl.$this->getQueryString($options);

        return $link;
    }


    public function getAccessToken($responseParams){
        if(!isset($responseParams['code']) || is_null($responseParams['code']))
            throw new ShopifyException('No Shopify access code provided');

        $params = $this->generateTokenRequestParams ( $responseParams['code'] );

        $link = $this->sanitizeUrl($this->getStoreUrl()) . $this->tokenUrl;

        $response = $this->client->post($link, [], $params)->send();

        return $response->json();
    }



    public function generateScope($scopes)
    {
        $formattedScope = [];
        if(!is_array($scopes))
            throw new ShopifyException('Expecting scopes to be an array');

        foreach($scopes as $resource => $actions){
            if(!is_array($actions))
                throw new ShopifyException('Invalid scope format');

            foreach($actions as $action){
                $formattedScope[] = $action.'_'.$resource;
            }
        }

        return implode(',', $formattedScope);
    }


    public function prepare()
    {
        $this->url = $this->sanitizeUrl($this->storeUrl).$this->resource;
        return $this;
    }


    public function sanitizeUrl($storeUrl)
    {
       return ( strpos ( $storeUrl, "http" )!==false ) ? str_replace ( "http", "https", $storeUrl ) : "https://" . $storeUrl;
    }



    public function get($resourceId = null, $options = [])
    {
        $this->url = $this->getUrl();

        if(!is_null($resourceId))
            $this->url = $this->appendResourceId($this->url, $resourceId);

        $this->url = $this->jsonizeUrl($this->url);

        $headers = $this->getHeaders();

        $response = $this->client->get($this->url, $headers, $options)->send();

        return $response->json();
    }


    public function jsonizeUrl($url)
    {
        return $url.'.json';
    }


    public function getQueryString($options)
    {
        if(sizeof($options) > 0){
            return '?'.http_build_query($options);
        }
    }


    public function appendResourceId($url, $productId)
    {
        return $url.'/'.$productId;
    }


    public function generateTokenRequestParams ( $code )
    {
        return [
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'code' => $code
        ];
    }

    /**
     * @return array
     * @throws ShopifyException
     */
    public function getAuthorizationOptions ()
    {
        $options = [
            'client_id' => $this->getClientId(),
            'scope' => $this->generateScope ( $this->getScopes() ),
            'redirect_uri' => $this->getRedirectUrl()
        ];
        return $options;
    }


    public function getClientId()
    {
        $clientId = config('shopify.CLIENT_ID');

        if(is_null($clientId))
        {
            throw new ShopifyException('No Shopify Client ID provided');
        }

        return $clientId;
    }


    public function getClientSecret()
    {
        $clientSecret = config('shopify.CLIENT_SECRET');

        if(is_null($clientSecret))
        {
            throw new ShopifyException('No Shopify Client Secret provided');
        }

        return $clientSecret;
    }


    public function getScopes()
    {
        $scopes = config('shopify.SCOPE');

        if(sizeof($scopes) <= 0)
        {
            throw new ShopifyException('No application scope has been provided');
        }
        return $scopes;
    }


    public function getStoreToken()
    {
        if(is_null($this->storeToken))
        {
            throw new ShopifyException('Access token not provided');
        }
        return $this->storeToken;
    }


    public function getStoreUrl()
    {
        if(is_null($this->storeUrl))
        {
            throw new ShopifyException('Shop url not provided');
        }
        return $this->storeUrl;
    }


    public function getUrl(){
        return $this->sanitizeUrl($this->getStoreUrl()).$this->resource;
    }

    public function getHeaders()
    {
        return [
            'X-Shopify-Access-Token' => $this->getStoreToken()
        ];
    }

    public function post ( $post_data )
    {
        return $this->client->post($this->url, $this->getHeaders(), $post_data)->send();
    }

    public function put ( $id, $modify_data )
    {
        $this->url = $this->jsonizeUrl($this->appendResourceId($this->getUrl(), $id));

        return $this->client->put($this->url, $this->getHeaders(), $modify_data)->send();
    }

    public function delete ( $id )
    {
        $this->url = $this->jsonizeUrl($this->appendResourceId($this->getUrl(), $id));

        return $this->client->delete($this->url, $this->getHeaders())->send();
    }

    public  function getRedirectUrl ()
    {
        $redirect = config('shopify.INSTALLATION_REDIRECT_URL');

        if(is_null($redirect))
        {
            throw new ShopifyException('No Redirect url provided');
        }

        return $redirect;
    }


}