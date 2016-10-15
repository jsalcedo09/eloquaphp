<?php

namespace EloquaApi\Service;

use EloquaApi\Eloqua;

abstract class AbstractService {

	/**
	 * @var GetEloqua
	 */
	protected $client;

	public function __construct( Eloqua $client ) {

		$this->client = $client;

	}

}