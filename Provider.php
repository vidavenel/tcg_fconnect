<?php

namespace Aidev\Fconnect;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User;
use SocialiteProviders\Manager\Oauth2\AbstractProvider;

class Provider extends AbstractProvider
{
    const IDENTIFIER = 'FCONNECT';

    protected $scopes = ['openid', 'profile', 'address ', 'phone', 'email'];

    protected $scopeSeparator = ' ';

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        //dd($this->buildAuthUrlFromBase('https://fcp.integ01.dev-franceconnect.fr/api/v1/authorize', $state)."&nonce=".sha1(mt_rand(0,mt_getrandmax())));
        return $this->buildAuthUrlFromBase('https://fcp.integ01.dev-franceconnect.fr/api/v1/authorize', $state)."&nonce=".sha1(mt_rand(0,mt_getrandmax()));
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://fcp.integ01.dev-franceconnect.fr/api/v1/token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param string $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://fcp.integ01.dev-franceconnect.fr/api/v1/userinfo/?schema=openid', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array $user
     * @return User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['sub'],
            'nickname' => null,
            'name' => $user['given_name'] . ' ' .$user['family_name'],
            'email'    => $user['email'],
            'avatar' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->mapUserToObject($this->getUserByToken(
            $token = Arr::get($response, 'access_token')
        ));
        $user['id_token'] = Arr::get($response, 'id_token');
        return $user->setToken($token)
            ->setRefreshToken(Arr::get($response, 'refresh_token'))
            ->setExpiresIn(Arr::get($response, 'expires_in'));
    }
}