<?php namespace A4M\Zencoder;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Requests;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * A command to fetch notifications from zencoder
 */
class FetcherCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'zencoder:notifications';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fetches notifications from zencoder.';

	protected $endpoint = '.zencoder.com/api/v2/notifications.json';

	protected $fetcherVersion = '0.2.8';

	protected $since;

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
		if ($this->option('docs')) {
			$this->showHelp();
			return;
		}

		if ($this->option('loop')) {
			if ($this->option('interval') < 15) {
				if (!$this->confirm('An interval of less than 15 seconds will quickly exhaust your api limit are you sure you wish to continue? [y/N] ', false)) {
					return;
				}
			}

			while (true) {
				if (!$this->getNotifications()) {
					break;
				}
				$interval = $this->option('interval');
				for ($i = 0; $i < $interval; $i++) {
					$this->output->write("\rWaiting " . number_format($interval - $i) . " seconds...");
					sleep(1);
				}
				$this->output->write("\r");
			}
		} else {
			$this->getNotifications();
		}
	}

	public function showHelp() {
		$this->line('This command is a laravel port of the fetcher ruby package');
		$this->line('For full documentation please see https://app.zencoder.com/docs/guides/advanced-integration/getting-zencoder-notifications-while-developing-locally');

		$this->line('');
		$this->comment('Using fetcher:');
		$this->line('To use fetcher when sending your API request to Zencoder use <info>http://zencoderfetcher/</info> as the url.');
		$this->line('next call <info>php artisan zencoder:notifications</info> to get fetcher to pull down the notification set from your account');

		$this->line('');
		$this->comment('Specifying your api key:');
		$this->line('This can be specified by passing your key as the first parameter or in the configuration file <info>php artisan config:publish a4m/zencoder</info>');

		$this->line('');
		$this->comment('Setting the receiving url:');
		$this->line('The receiving url is where fetcher will send your notifications after they have been received - remember we are just a proxy');
		$this->line('The receiving url can be specified by using the <info>--url</info> option or in the configuration file <info>php artisan config:publish a4m/zencoder</info>');

		$this->line('');
		$this->comment('Running fetcher in a loop:');
		$this->line('By default fetcher will only run once and then need to be called again manually, this can be setup to run in a loop using the <info>--loop</info> option');
		$this->line('The delay in seconds between runs can be set using the <info>--interval</info> option <comment>(default ' . $this->option('interval') . ')</comment>');

		$this->line('');
		$this->comment('Controlling received notifications:');
		$this->line('The number of notifications received can be specified using the <info>--count</info> option <comment>(default ' . $this->option('count') . ')</comment>');
		$this->line('<error>Warning:</error> Notifications via the fetcher are not filtered to notifications that haven\'t been sent before. If you have created 1 new job since your last request, and request 10, you\'ll get the last 10 jobs.');
		$this->line('Be sure to handle this in your application. You can also use this to your advantage when developing as you will not have to create a new job every time changes are made to your code.');

		$this->line('');
		$this->comment('Retrieving output from your notifications');
		$this->line('Sometimes you might want to see what your server is returning when you send through the notifications, Just use the <info>--verbose</info> flag and any content returned from the server will be written to the console');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments() {
		return [
			['key', InputArgument::OPTIONAL, 'Your api key (Full access key)', \Config::get('zencoder::apiKey')],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return [
			['docs', 'd', InputOption::VALUE_NONE, 'Shows the in-depth documentation'],
			['url', "u", InputOption::VALUE_OPTIONAL, 'Specifies the URL to post the notifications.', \Config::get('zencoder::receivingUrl')],
			['loop', "l", InputOption::VALUE_NONE, 'Indicates that the notifier should be run in a loop.'],
			['interval', "x", InputOption::VALUE_OPTIONAL, 'Specifies the number of seconds to wait between checks for new notifications if looping. (default 60)', 60],
			['count', 'c', InputOption::VALUE_OPTIONAL, 'Specifies the number of notifications to retrieve. (default 50)', 50],
			['page', 'p', InputOption::VALUE_OPTIONAL, 'Specifies the page to load.', 1],
			['endpoint', 'e', InputOption::VALUE_OPTIONAL, 'The endpoint (Truth be told I have no idea!)', 'app'],
			['since', 'm', InputOption::VALUE_OPTIONAL, 'Load notifications starting since n minutes ago'],
		];
	}

	private function getNotifications() {

		$this->comment('Fetching ' . $this->option('count') . ' notifications');
		$options = [
			'api_key'  => $this->argument('key'),
			'per_page' => $this->option('count'),
			'page'     => $this->option('page'),
			"version"  => "latest"
		];

		if ($this->option('since')) {
			$options['since'] = Carbon::now()->subMinutes($this->option('since'))->toISO8601String();
		}


		$request = Requests::request('https://' . $this->option('endpoint') . $this->endpoint, [
			'HTTP_X_FETCHER_VERSION' => $this->fetcherVersion
		], $options);

		$response = json_decode($request->body);

		if (!$request->success || isset($response->errors)) {
			$errors = isset($response->errors) ? implode("\n", $response->errors) : '';
			$this->error("There was an error fetching the notifications: (code {$request->status_code})\n{$errors}");
			return false;
		}elseif (count($response->notifications) === 0) {
			$this->info('No notifications found');
		} else {
			$this->info(count($response->notifications).' notifications fetched successfully');
			$this->comment('Sending notifications to '.$this->option('url'));
			foreach($response->notifications as $notification){
				$this->sendNotification($notification);
			}
		}

		return true;
	}

	public function sendNotification($notification){
		$request = Requests::post($this->option('url'), [], json_encode($notification));
		if(!$request->success){
			$this->error("There was an error forwarding the notifications: (code {$request->status_code})");
			if($this->option('verbose') || $this->confirm('Would you like to see the output? [Y/n] ', true)){
				$this->line($request->body);
			}
			return false;
		} else {
			if($this->option('verbose')){
				$this->line($request->body);
			}
			$this->info('Notifications sent');
		}
	}

}
