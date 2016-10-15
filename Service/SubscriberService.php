<?php

namespace EloquaApi\Service;

class SubscriberService extends AbstractService {

	public function add( $subscriber ) {

		$data = new \stdClass();
		$data->subscribers = array( $subscriber );

		return $this->client->request( 'subscribers', 'post', $data );

	}

}