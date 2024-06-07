<?php

declare(strict_types=1);

/*
 * This file is part of the MediaEase project.
 *
 * (c) Thomas Chauveau <contact.tomc@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

final class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private HttpClientInterface $httpClient)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        try {
            $targetUrl = $this->urlGenerator->generate('app_home');

            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $host = $request->server->get('HTTP_HOST');
            $jwtAccessToken = $this->getJwtAccessToken($username, $password, $host);
            $expire = time() + 3600;
            $cookie = new Cookie(
                'thegate',
                $jwtAccessToken,
                $expire,
                '/',
                null,
                false,
                false,
                false,
                'lax'
            );
            $redirectResponse = new RedirectResponse($targetUrl);
            $redirectResponse->headers->setCookie($cookie);

            return $redirectResponse;
        } catch (\Exception) {
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }
    }

    private function getJwtAccessToken(string $username, string $password, string $host): string
    {
        $url = sprintf('http://%s/api/auth/login', $host);
        $apiResponse = $this->sendRequest($url, $username, $password);

        return $apiResponse['token'];
    }

    /**
     * @return array<string, string>
     */
    private function sendRequest(string $url, string $username, string $password): array
    {
        $apiResponse = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'username' => $username,
                'password' => $password,
            ],
        ]);

        return $apiResponse->toArray();
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
