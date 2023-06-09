<?php

// src/Security/LoginFormAuthenticator.php
namespace App\Security;

use App\Entity\Usuario;
use App\Security\ActiveDirectoryService;
use App\Security\ActiveDirectoryUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $directoryUser;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, ActiveDirectoryUserService $directoryUser)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->directoryUser = $directoryUser;
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => explode('@', $request->request->get('_username'))[0],
            'password' => $request->request->get('_password'),
            'domain' => $request->request->get('_domain') ?: "local",
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username'],
            $credentials['domain']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?Usuario
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        $domain = $credentials["domain"];
        $user_database = $this->entityManager->getRepository(Usuario::class)->findOneByUsername($credentials["username"]);
        if(!$user_database)
        {
            throw new CustomUserMessageAuthenticationException("El usuario no está registrado en el sistema.");
        }
        else {
            if($domain  !== 'local') {
                try{
                    $result = $this->directoryUser->queryUserByAccountName($credentials['username']);
                    if (count($result) == 0) {
                        throw new CustomUserMessageAuthenticationException("El usuario no está registrado en el dominio.");
                    }
                } catch (ConnectionException $e){
                    throw new ConnectionException($e->getMessage());
                }

            }
            return $user_database;
        }
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if($credentials['domain'] === 'local'){
            return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        } else {
            try{
                $result = $this->directoryUser->queryUserByAccountName($credentials['username']);
                $user_ldap = $result[0];
                $result = $this->directoryUser->userCheckPassword($user_ldap->getDn(), $credentials['password']);
            }
            catch (\Exception $e){
                return false;
            }
            return $result;
        }

    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        return new RedirectResponse(($this->urlGenerator->generate('app_index')));
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
