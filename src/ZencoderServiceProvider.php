<?php namespace A4M\Zencoder;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class ZencoderServiceProvider extends ServiceProvider {

	public function boot() {

		$this->package('GC-Mark/Laravel-Zencoder', 'zencoder');
		$this->app['zencoder'] = $this->app->share(function ($app) {

			/**
			 * @var \Illuminate\Config\Repository $config
			 */
			$config          = $app['config'];
			if ($config->get('zencoder::integrationMode')) {
				$apiKey = $config->get('zencoder::integrationModeApiKey');
			} else {
				$apiKey = $config->get('zencoder::apiKey');
			}

			return new Zencoder(
				$apiKey,
				$config->get('zencoder::apiVersion'),
				$config->get('zencoder::apiHost'),
				$config->get('zencoder::apiDebug')
			);


		});

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

		$this->app['command.zencoder.fetcher'] = $this->app->share(function($app){
			return new FetcherCommand;
		});

		$this->commands(['command.zencoder.fetcher']);

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('zencoder', 'command.zencoder.fetcher');
	}

	public function guessPackagePath()
	{
		$path = with(new ReflectionClass($this))->getFileName();

		return realpath(dirname($path).'/');
	}

}
