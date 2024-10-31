<?php

class Campaigns {

	public function __construct(SendmachineApiClient $master) {
		$this->master = $master;
	}

	/**
	 *get campaigns
	 * @param type $filter (campaign, transactional, archived, new, finalized, scheduled, sending, sent, cancelled)
	 * @param type $orderby (name, cdate, total, opened, openedratio, clicked, clickedratio)
	 * @param type $offset
	 * @param type $limit
	 * @param type $search
	 * @return array
	 * {
	 *    "campaign": [
	 *        {
	 *            "campaign_id",
	 *            "name",
	 *            "cdate",
	 *            "mdate",
	 *            "autocreated",
	 *            "state",
	 *            "body_html",
	 *            "schedule",
	 *            "archived"
	 *        },
	 *		...
	 *    ],
	 *    "total"
	 * }
	 */
	public function get($filter = 'all', $orderby = 'cdate', $offset = 0, $limit = 25, $search = null) {

		$params = array('filter' => $filter, 'orderby' => $orderby, 'offset' => $offset, 'limit' => $limit, 'search' => $search);
		return $this->master->request('/campaigns', 'GET', $params);
	}

	/**
	 * 
	 * @param array $options
	 * @return array
	 * 
	 */
	public function create($options = array()) {

		return $this->master->request('/campaigns', 'POST', $options);
	}

	/**
	 * Get campaign details (body is not sent here)
	 * @param int $campaign_id
	 * @return array
	 * {
	 *    "campaign": {
	 *        "campaign_id",
	 *        "name",
	 *        "cdate",
	 *        "mdate",
	 *        "autocreated",
	 *        "subject",
	 *        "sender_name",
	 *        "replyto",
	 *        "schedule",
	 *        "state",
	 *        "archived",
	 *        "contactlist_id",
	 *        "segment_id",
	 *        "ga_tracking",
	 *        "personalize_to",
	 *        "contactlist_name",
	 *        "sender_email"
	 *    }
	 * }
	 */
	public function details($campaign_id) {

		return $this->master->request('/campaigns/' . $campaign_id, 'GET');
	}

	/**
	 * Update campaign
	 * @param int $campaign_id
	 * @param array $data
	 * @return array
	 * {
	 *    "status"
	 * }
	 */
	public function update($campaign_id, $data = array()) {

		return $this->master->request('/campaigns/' . $campaign_id, 'POST', $data);
	}
	
	/**
	 * schedule campaign
	 * @param int $campaign_id
	 * @param date $date
	 * @return array
	 * {
	 *	 "status"
	 * }
	 */
	public function schedule($campaign_id, $date = "") {

		$params = array('date' => $date);
		return $this->master->request('/campaigns/schedule/' . $campaign_id, 'POST', $params);
	}

	/**
	 * unschedule campaign
	 * @param int $campaign_id
	 * @return array
	 * {
	 * 	 "status"
	 * }
	 */
	public function unschedule($campaign_id) {

		return $this->master->request('/campaigns/schedule/' . $campaign_id, 'DELETE');
	}

	/**
	 * send test email
	 * @param int $campaign_id
	 * @param string $addresses
	 * @return array
	 * {
	 * 	 "status"
	 * }
	 */
	public function test($campaign_id, $addresses = "") {

		$params = array('addresses' => $addresses);
		return $this->master->request('/campaigns/test/' . $campaign_id, 'POST', $params);
	}

	/**
	 * send campaign
	 * @param int $campaign_id
	 * @return array
	 * {
	 * 	 "status"
	 * }
	 */
	public function send($campaign_id) {

		return $this->master->request('/campaigns/send/' . $campaign_id, 'POST');
	}

	/**
	 * archive campaign
	 * @param int $campaign_id
	 * @return array
	 * {
	 * 	 "status"
	 * }
	 */
	public function archive($campaign_id) {

		return $this->master->request('/campaigns/archive/' . $campaign_id, 'POST');
	}

	/**
	 * unarchive campaign
	 * @param int $campaign_id
	 * @return array
	 * {
	 * 	 "status"
	 * }
	 */
	public function unarchive($campaign_id) {

		return $this->master->request('/campaigns/archive/' . $campaign_id, 'DELETE');
	}

	/**
	 * check if campaign is ready for sending
	 * @param int $campaign_id
	 * @return array
	 * {
	 * 	 "status"
	 * }
	 */
	public function ready($campaign_id) {

		return $this->master->request('/campaigns/ready/' . $campaign_id, 'GET');
	}

	/**
	 * duplicate campaign
	 * @param int $campaign_id
	 * @return array
	 * {
	 * 	 "status",
	 *	 "new_id"
	 * }
	 */
	public function duplicate($campaign_id) {

		return $this->master->request('/campaigns/duplicate/' . $campaign_id, 'POST');
	}

	/**
	 * get campaign html and text content
	 * @param int $campaign_id
	 * @return array
	 * {
	 *    "source": {
	 *        "body_text",
	 *        "body_html"
	 *    }
	 * }
	 */
	public function content($campaign_id) {

		return $this->master->request('/campaigns/content/' . $campaign_id, 'GET');
	}

}