<?php namespace A4M\Zencoder;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use Services_Zencoder;

class Zencoder extends Services_Zencoder {

	/**
	 * Returns the instance
	 * Allows direct access to the instance properties
	 *
	 * @return $this
	 */
	public function instance() {
		return $this;
	}

	/**
	 * Provides access the Zencoder Accounts API
	 *
	 * Valid functions: create, details, integration, live
	 *
	 * @return \Services_Zencoder_Accounts
	 */
	public function accounts() {
		return $this->accounts;
	}

	/**
	 * Provides access the Zencoder Notifications API
	 *
	 * Valid functions: parseIncoming
	 *
	 * @return \Services_Zencoder_Notifications
	 */
	public function notifications() {
		return $this->notifications;
	}

	/**
	 * Provides access the Zencoder Jobs API
	 *
	 * Valid functions: create, index, details, progress, resubmit, cancel
	 *
	 * @return \Services_Zencoder_Jobs
	 */
	public function jobs() {
		return $this->jobs;
	}

	/**
	 * Provides access the Zencoder Inputs API
	 *
	 * Valid functions: details, progress
	 *
	 * @return \Services_Zencoder_Inputs
	 */
	public function inputs() {
		return $this->inputs;
	}

	/**
	 * Provides access the Zencoder Outputs API
	 *
	 * Valid functions: details, progress
	 *
	 * @return \Services_Zencoder_Outputs
	 */
	public function outputs() {
		return $this->outputs;
	}

}