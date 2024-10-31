<?php

class Mail {

	public function __construct(SendmachineApiClient $master) {
		$this->master = $master;
	}

	/**
	 * send mail
	 * @param array $details
	 * @return array
	 * {
	 *    "sent"
	 *    "status"
	 * }
	 */
	public function send($details) {

		return $this->master->request('/mail/send', 'POST', $details);
	}

}