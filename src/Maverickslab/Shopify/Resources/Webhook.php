<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/18/15
 * Time: 10:29 AM
 */

namespace Maverickslab\Shopify\Resources;


use Maverickslab\Shopify\ApiRequestor;
use Maverickslab\Shopify\Exceptions\ShopifyException;

class Webhook extends BaseResource{


    public function __construct (ApiRequestor $requestor)
    {
        $this->requestor = $requestor;
        $this->requestor->resource = '/admin/webhooks';
    }

    public function install($hook = null){
        $webHooks = $this->generateWebhooks();
        if(!is_null($hook)){
            $webHook = [
                'webhook' => [
                    'topic'   => $hook,
                    'address' => $this->getWebhookUrl(),
                    'format'  => 'json'
                ]
            ];
            $this->requestor->post($webHook);
        }else{
            foreach ($webHooks as $resource => $events) {
                foreach ($events as $event) {
                    $webHook = [
                        'webhook' => [
                            'topic'   => $resource . '/' . $event,
                            'address' => $this->getWebhookUrl(),
                            'format'  => 'json'
                        ]
                    ];
                    $this->requestor->post($webHook);
                }
            }
        }

    }

    private function generateWebhooks ()
    {
        $webhooks = config('shopify.WEBHOOKS');

        if(sizeof($webhooks) <= 0)
        {
            throw new ShopifyException('No Resource webhook provided');
        }
        return $webhooks;
    }

    private function getWebhookUrl ()
    {
        $webhook_installation_url = config('shopify.WEBHOOK_INSTALLATION_URL');

        if(is_null($webhook_installation_url))
        {
            throw new ShopifyException('No webhook installation url provided');
        }

        return $webhook_installation_url;
    }
}