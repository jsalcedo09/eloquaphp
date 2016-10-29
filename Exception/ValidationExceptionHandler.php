<?php

namespace EloquaApi\Exception;

class ValidationExceptionHandler {

	/**
	 * @var string
	 */
	protected $error;

	public function __construct( $body ) {

		$this->error = json_decode( $body )->errors[0];

	}

	public function handle() {

		$error_slug = ucwords( $this->error->code, '_' );
		$exception_class = str_replace( "_", "", $error_slug );
		$exception_class = $exception_class . 'Exception';
		$class = '\\EloquaApi\\Exception\\' . $exception_class;

		if ( class_exists( $class ) ) {
			throw new $class( $this->error->message );
		}

		throw new \Exception( $this->error->message );

	}

}