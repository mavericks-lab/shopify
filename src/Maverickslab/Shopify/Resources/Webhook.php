<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/18/15
 * Time: 10:29 AM
 */

namespace Maverickslab\Shopify\Resources;


use Maverickslab\Shopify\ApiRequestor;

class Webhook extends BaseResource{


    public function __construct (ApiRequestor $requestor)
    {
        $this->requestor = $requestor;
        $this->requestor->resource = '/admin/orders';
    }

    public function install(){
        $webHooks = $this->generateWebhooks();

        foreach ($webHooks as $resource => $events) {
            foreach ($events as $event) {
                $webHook = json_encode([
                    'webhook' => [
                        'topic'   => $resource . '/' . $event,
                        'address' => $this->getWebhookUrl(),
                        'format'  => 'json'
                    ]
                ]);
                //$response = self::sendRequest($url, "POST", $organizationAccount['user_token'], $webHook);
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
    }
}