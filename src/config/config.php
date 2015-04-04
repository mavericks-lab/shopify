<?php

return [

    'CLIENT_ID' => env('SHOPIFY_CLIENT_ID', NULL),

    'CLIENT_SECRET' => env('SHOPIFY_CLIENT_SECRET', NULL),

    'INSTALLATION_REDIRECT_URL' => env('SHOPIFY_INSTALLATION_REDIRECT_URL', NULL),

    'SCOPE' => [
        /*
         *  Specify the scope of your applicatiion by indicating the resource you will
         *  work with and indicate in an array the kind of action that will be performed
         * eg.
         * 'products' => ['read', 'write']
         */

    ]
];