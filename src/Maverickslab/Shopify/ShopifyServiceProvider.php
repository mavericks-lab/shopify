<?php namespace Maverickslab\Shopify;

use Illuminate\Support\ServiceProvider;

class ShopifyServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}


    public function boot(){
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('shopify.php'),
        ]);
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

}
