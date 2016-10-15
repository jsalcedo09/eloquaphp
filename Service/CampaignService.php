<?php

namespace EloquaApi\Service;

class CampaignService extends AbstractService {

	/**
	 * Returns all campaigns
	 *
	 * @param int $page
	 *
	 * @return \stdClass
	 */
	public function all( $page = 1 ) {

		return $this->client->request( 'campaigns', 'get', ['page' => $page] );

	}

	/**
	 * Adds a user to the specified campaign
	 *
	 * @param int $campaign_id
	 * @param \stdClass $subscriber
	 * @return \stdClass
	 */
	public function subscribe( $campaign_id, $subscriber ) {

		$data = new \stdClass();
		$data->subscribers = array( $subscriber );

		return $this->client->request( "campaigns/{$campaign_id}/subscribers", 'post', $data );

	}

}