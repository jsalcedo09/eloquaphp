<?php

namespace EloquaApi;

use EloquaApi\Exception\ServiceNotFoundException;
use EloquaApi\Exception\ValidationExceptionHandler;
use EloquaApi\OAuth2\Provider;
use EloquaApi\Service\CampaignService;
use EloquaApi\Service\SubscriberService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use League\OAuth2\Client\Token\AccessToken;

class Eloqua {

    protected $baseUrl = 'https://secure.p03.eloqua.com';

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var OAuth2\Provider
     */
    protected $authProvider;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var array
     */
    protected $refresh_callback;

    /**
     * @var array
     */
    protected $apis = array();

    public function __construct($config = array()) {

        if (isset($config['clientId'])) {
            $this->clientId = $config['clientId'];
        }

        if (isset($config['clientSecret'])) {
            $this->clientSecret = $config['clientSecret'];
        }

        if (isset($config['redirectUri'])) {
            $this->redirectUri = $config['redirectUri'];
        }
    }

    /**
     * @return string
     */
    public function get_clientId() {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function set_clientId($clientId) {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function get_clientSecret() {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function set_clientSecret($clientSecret) {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function get_redirectUri() {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     */
    public function set_redirectUri($redirectUri) {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return OAuth2\Provider
     */
    public function get_authProvider() {

        if (!$this->authProvider) {
            $config = array(
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'redirectUri' => $this->redirectUri,
            );
            $this->authProvider = new Provider($config);
        }

        return $this->authProvider;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function get_httpClient() {
        if (!$this->httpClient) {
            $this->httpClient = new Client(array(
                'base_uri' => $this->baseUrl,
                'headers' => array(
                    'Authorization' => 'Bearer ' . $this->token->getToken(),
                ),
                    ));
        }

        return $this->httpClient;
    }

    /**
     * @param Token $token
     */
    public function set_token(AccessToken $token) {

        $this->token = $token;
    }

    /**
     * @return SubscriberService
     */
    public function subscribers() {

        return $this->get_api('SubscriberService');
    }

    /**
     * @return CampaignService
     */
    public function campaigns() {

        return $this->get_api('CampaignService');
    }

    public function get_api($class) {
        $fq_class = '\\EloquaApi\\Service\\' . $class;

        if (!class_exists($fq_class)) {
            throw new ServiceNotFoundException('Service: ' . $class . ' could not be found');
        }

        if (!array_key_exists($fq_class, $this->apis)) {
            $this->apis[$fq_class] = new $fq_class($this);
        }

        return $this->apis[$fq_class];
    }

    public function request($path = '', $method = 'get', $data = array()) {

        if (empty($this->baseUrl)) {
            //$this->baseUrl = $this->getBaseUrl();
        }

        if (!$this->validate_token()) {
            throw new Exception("Invalid Token");
        }

        $options = array();

        switch ($method) {
            case 'get' :
                if (!empty($data)) {
                    $query = array();
                    foreach ($data as $key => $value) {
                        $query[$key] = $value;
                    }
                    $options['query'] = $query;
                }
                break;
            case 'post' :
            case 'put' :
                if (!empty($data)) {
                    $json = array();
                    foreach ($data as $key => $value) {
                        $json[$key] = $value;
                    }
                    $options['json'] = $json;
                }
                break;
        }

        try {
            /** @var \GuzzleHttp\Psr7\Response $response * */
            $response = $this->get_httpClient()->{$method}($path, $options);
            return json_decode($response->getBody());
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 422) {
                $body = $e->getResponse()->getBody()->getContents();
                $handler = new ValidationExceptionHandler($body);
                $handler->handle();
            } else if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
                
            } else if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 409) {
                return 'uniqueValidationError';
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return false;
    }

    protected function getBaseUrl() {

        try {
            if ($this->token) {
                $client = new Client(array(
                    'base_uri' => 'https://login.eloqua.com',
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $this->token->getToken(),
                    ),
                ));
                $response = $client->get('id');
                $json = json_decode($response->getBody());
                return $json->urls->base;
            }
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 422) {
                $body = $e->getResponse()->getBody()->getContents();
                $handler = new ValidationExceptionHandler($body);
                $handler->handle();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function set_refresh_callback($function) {
        $this->refresh_callback = $function;
    }

    private function validate_token() {
        $provider = $this->get_authProvider();
        if (!empty($this->token) && $this->token->hasExpired()) {
            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $this->token->getRefreshToken()
            ]);

            $this->set_token($newAccessToken);
            call_user_func_array($this->refresh_callback, array($newAccessToken));
        } else if (empty($this->token)) {
            return false;
        }

        return true;
    }

}
