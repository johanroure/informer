<?php namespace Blakmoder\Informer;

use Illuminate\Support\ServiceProvider;

class InformerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('blakmoder/informer');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['informer'] = $this->app->share(function($app)
		{
			$obj = new Informer( $app['config']->get('informer::themvdb-key') );
			return $obj;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('informer');
	}

}
