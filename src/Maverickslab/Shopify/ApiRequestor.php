<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/16/15
 * Time: 10:56 PM
 */

namespace Maverickslab\Shopify;


use Guzzle\Http\Exception\ClientErrorResponseException;
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

        try{
            $response = $this->client->post($link, [], $params)->send();

            return $response->json();
        }catch (ClientErrorResponseException $exception){
            throw new ShopifyException( $exception->getMessage(), $exception->getResponse()->json());
        }
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
        $url = $storeUrl;
        if( $this->hasProtocol ( $storeUrl ) )
        {
            if( ! $this->protocolIsHttps ( $storeUrl ) )
            {
                $url = str_replace ( "http", "https", $storeUrl );
            }
            return $url;
        }
        return "https://" . $storeUrl;
    }


    public function get($resourceId = null, $options = [])
    {
        try{
            $this->url = $this->getUrl();

            if(!is_null($resourceId)) $this->url = $this->appendResourceId($this->url, $resourceId);

            $this->url = $this->jsonizeUrl($this->url);

            $headers = $this->getHeaders();

            $response = $this->client->get($this->url, $headers, $options)->send();

            return $response->json();
        }catch (ClientErrorResponseException $exception){
            throw new ShopifyException( $exception->getMessage(), $exception->getResponse()->json());
        }
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
            'X-Shopify-Access-Token' => $this->getStoreToken(),
            'Content-Type' => 'application/json'
        ];
    }

    public function post ( $post_data )
    {
        try{
            $this->url = $this->jsonizeUrl($this->getUrl());
            $response = $this->client->post($this->url, $this->getHeaders(), json_encode($post_data))->send();
            return $response->json();
        }catch (ClientErrorResponseException $exception){
            throw new ShopifyException( $exception->getMessage(), $exception->getResponse()->json());
        }
    }

    public function put ( $id, $modify_data )
    {
        try{
            $this->url = $this->jsonizeUrl($this->appendResourceId($this->getUrl(), $id));

            $response = $this->client->put($this->url, $this->getHeaders(), json_encode($modify_data))->send();

            return $response->json();
        }catch (ClientErrorResponseException $exception){
            throw new ShopifyException( $exception->getMessage(), $exception->getResponse()->json());
        }
    }

    public function delete ( $id )
    {
        try{
            $this->url = $this->jsonizeUrl($this->appendResourceId($this->getUrl(), $id));

            $response = $this->client->delete($this->url, $this->getHeaders())->send();
            return $response->json();
        }catch (ClientErrorResponseException $exception){
            throw new ShopifyException( $exception->getMessage(), $exception->getResponse()->json());
        }
    }

    public function count(){
        $this->url = $this->getUrl().'/count';

        $this->url = $this->jsonizeUrl($this->url);
        $headers = $this->getHeaders();
        $response = $this->client->get($this->url, $headers)->send();

        return $response->json();
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


    private function hasProtocol ( $storeUrl )
    {
        return strpos ( $storeUrl, "http" )!== false;
    }

    /**
     * @param $storeUrl
     * @return bool
     */
    private function protocolIsHttps ( $storeUrl )
    {
        return (strpos ( $storeUrl, "https" ) !== false ) ? true : false;
    }


}