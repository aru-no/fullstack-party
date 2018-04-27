<?php
namespace Auth;

use \League\OAuth2\Client\Provider\Github as GithubOAuthClient;
use Milo\Github\OAuth\Token;

class AuthService
{
    /**
     * @var array
     */
    private $config;

    /**
     * AuthService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $token
     */
    public static function setToken(string $token): void
    {
        $_SESSION['oauth2token'] = $token;
    }

    /**
     * @return string
     */
    public static function getToken(): string
    {
        return (string) $_SESSION['oauth2token'];
    }

    /**
     * @param array $queryParams
     * @return bool
     */
    public function authorize(array $queryParams): void
    {
        $client = new GithubOAuthClient($this->config);

        if (!isset($queryParams['code'])) {
            $authUrl = $client->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $client->getState();
            header('Location: '.$authUrl);

            exit;

        } elseif (empty($queryParams['state']) || ($queryParams['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);

            exit('Invalid state');

        } else {
            /** @var Token $token */
            $token = $client->getAccessToken('authorization_code', [
                'code' => $queryParams['code']
            ]);

            self::setToken($token->getToken());
        }
    }

    /**
     *
     */
    public function logout()
    {
        self::setToken('');
    }
}