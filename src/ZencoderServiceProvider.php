<?php namespace A4M\Zencoder;

use Illuminate\Support\ServiceProvider;

class ZencoderServiceProvider extends ServiceProvider {

	public function boot() {

		$this->package('a4m/zencoder', 'zencoder');
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

			return new \Services_Zencoder(
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
	public function register() { }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('zencoder');
	}

}