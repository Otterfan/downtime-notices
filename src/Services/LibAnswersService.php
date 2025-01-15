<?php

namespace App\Services;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class LibAnswersService
{
    private string $client_id;
    private string $client_secret;
    private ?string $auth_token = null;
    private const TOKEN_URL = 'https://answers.bc.edu/api/1.1/oauth/token';
    private const API_URL = "https://answers.bc.edu/api/1.1/faqs";

    public function __construct(string $client_id, string $client_secret, Client)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    /**
     * @throws \Exception
     */
    public function query(string $question_id)
    {
        if (is_null($this->auth_token)) {
            $this->getToken();
        }

        $url = self::API_URL . "/${question_id}";
    }

    /**
     * Get a new authorization token
     *
     * @return void
     * @throws \Exception
     */
    private function getToken(): void
    {
        $provider = new GenericProvider([
            'clientId' => $this->client_id,    // The client ID assigned to you by the provider
            'clientSecret' => $this->client_secret,    // The client password assigned to you by the provider
            'urlAuthorize' => self::TOKEN_URL,
            'urlAccessToken' => self::TOKEN_URL,
            'urlResourceOwnerDetails' => null
        ]);

        try {
            $this->auth_token = $provider->getAccessToken('client_credentials');
        } catch (IdentityProviderException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
