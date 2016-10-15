<?php

namespace EloquaApi\OAuth2;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Provider extends AbstractProvider {

	public $domain = 'https://login.eloqua.com';

	public $scopes = ['full'];

	private $urlResourceOwnerDetails;

	private $responseError = 'error';

	private $responseCode;


	/**
	 * Get the URL that this provider uses to request user details.
	 * Since this URL is typically an authorized route, most providers will require you to pass the access_token as
	 * a parameter to the request. For example, the google url is:
	 * 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token='.$token
	 *
	 * @param AccessToken $token
	 * @return string
	 */
	public function urlUserDetails( AccessToken $token ) {
		// TODO: Implement urlUserDetails() method.
	}

	/**
	 * Given an object response from the server, process the user details into a format expected by the user
	 * of the client.
	 *
	 * @param object      $response
	 * @param AccessToken $token
	 * @return mixed
	 */
	public function userDetails( $response, AccessToken $token ) {
		// TODO: Implement userDetails() method.
	}

	/**
	 * Returns the base URL for authorizing a client.
	 *
	 * Eg. https://oauth.service.com/authorize
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl()
	{
		return $this->domain . '/auth/oauth2/authorize';
	}

	/**
	 * Returns the base URL for requesting an access token.
	 *
	 * Eg. https://oauth.service.com/token
	 *
	 * @param array $params
	 * @return string
	 */
	public function getBaseAccessTokenUrl(array $params)
	{
		return $this->domain . '/auth/oauth2/token';
	}

	/**
	 * Returns the URL for requesting the resource owner's details.
	 *
	 * @param AccessToken $token
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl(AccessToken $token)
	{
		return $this->urlResourceOwnerDetails;
	}

	/**
	 * Returns the default scopes used by this provider.
	 *
	 * This should only be the scopes that are required to request the details
	 * of the resource owner, rather than all the available scopes.
	 *
	 * @return array
	 */
	protected function getDefaultScopes()
	{
		return $this->scopes;
	}

	/**
	 * Checks a provider response for errors.
	 *
	 * @throws IdentityProviderException
	 * @param  ResponseInterface $response
	 * @param  array|string $data Parsed response data
	 * @return void
	 */
	protected function checkResponse(ResponseInterface $response, $data)
	{
		if (!empty($data[$this->responseError])) {
			$error = $data[$this->responseError];
			$code  = $this->responseCode ? $data[$this->responseCode] : 0;
			throw new IdentityProviderException($error, $code, $data);
		}
	}

	/**
	 * Returns the authorization headers used by this provider.
	 *
	 * Typically this is "Bearer" or "MAC". For more information see:
	 * http://tools.ietf.org/html/rfc6749#section-7.1
	 *
	 * No default is provided, providers must overload this method to activate
	 * authorization headers.
	 *
	 * @param  mixed|null $token Either a string or an access token instance
	 * @return array
	 */
	 protected function getDefaultHeaders()
	 {
		return ['Authorization' => 'Basic '.base64_encode($this->clientId.":".$this->clientSecret)];
	 }

	/**
	 * Generates a resource owner object from a successful resource owner
	 * details request.
	 *
	 * @param  array $response
	 * @param  AccessToken $token
	 * @return ResourceOwnerInterface
	 */
	protected function createResourceOwner(array $response, AccessToken $token)
	{
		return new GenericResourceOwner($response, $this->responseResourceOwnerId);
	}
}