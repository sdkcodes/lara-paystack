<?php

namespace Sdkcodes\LaraPaystack;

use Illuminate\Support\ServiceProvider;

/**
 * Lara-paystack service provider
 */
class LaraPaystackServiceProvider extends ServiceProvider
{
	
	public function boot(){
		$this->publishes([
			__DIR__.'/config/larapaystack.php' => config_path('larapaystack.php'),
		]);
		$this->mergeConfigFrom(
	        __DIR__.'/config/larapaystack.php', 'larapaystack'
	    );
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
	}

	public function register(){
		
	}
}