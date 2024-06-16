<?php

namespace TCK\Odbc;

use Illuminate\Support\ServiceProvider;

class OdbcServiceProvider extends ServiceProvider {

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

	/**
	 * Bootstrap the application events.
	 */
	public function boot(): void
    {
		$factory = $this->app['db'];
		$factory->extend('odbc', function ($config) {
			if (! isset($config['prefix'])) {
				$config['prefix'] = '';
			}

			$connector = new ODBCConnector();
			$pdo       = $connector->connect($config);

			return new ODBCConnection($pdo, $config['database'], $config['prefix']);
		});
	}
}
