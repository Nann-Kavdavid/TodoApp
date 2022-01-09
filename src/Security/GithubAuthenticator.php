<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubAuthenticator extends SocialAuthenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): bool
    {
        // 今のルートとチェック用ルートをマッチする場合だけ、継続できる
        return $request->attributes->get('_route') === 'connect_github_check';
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $githubUser = $this->getGithubClient()->fetchUserFromToken($credentials);

        $email = $githubUser->getEmail();

        // Githubでログインしたことがあるユーザ
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['gihubId' => $githubUser->getId()]);

        $githubData = $githubUser->toArray();

        if ($existingUser) {
            return $existingUser;
        }

        if (!$email) {
            $email = "{$githubUser->getId()}@githuboauth.com";
        }

        if ($existingUser) {
            $user = $existingUser;
        } else {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $user = new User();
                $user->setEmail($email);
                $user->setPassword(null);
            }
        }

        $user->setGithubId($githubUser->getId());
        $this->entityManager->persist();
        $this->entityManager->flush();

        return $user;
    }

    public function getCredential(Request $request): \League\OAuth2\Client\Token\AccessToken
    {
        return $this->fetchAccessToken($this->getGithubClient());
    }

    private function getGithubClient(): \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
    {
        // config/packages/knpu_oauth2_client.yaml にある 'github_main'
        return $this->clientRegistry->getClient('github_main');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // Todo: 自分のアプリのルートに変更してください。
        $targetUrl = $this->router->generate('pages_homepage');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}