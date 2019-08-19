<?php

namespace App\Security;

use App\Entity\Group;
use App\Entity\Setting;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Entry;
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
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class FormAuthenticator extends AbstractFormLoginAuthenticator {

    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $ldap;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, Ldap $ldap) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->ldap = $ldap;
    }

    public function supports(Request $request) {
        return 'login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    public function getCredentials(Request $request) {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
                Security::LAST_USERNAME,
                $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        $user = null;
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);
        if (!$user) {
            $this->ldap->bind();
            $result = $this->ldap->findUserQuery($credentials['username'])->execute();
            $count = \count($result);
            if (!$count) {
                throw new CustomUserMessageAuthenticationException('No se pudo encontrar al usuario');
            }
            if ($count > 1) {
                throw new CustomUserMessageAuthenticationException('Multiples usuarios encontrados en el Directorio Activo');
            }
            /** @var Entry * */
            $result = $result[0];
            $user = new User();
            try {
                $dn = $result->getAttribute('distinguishedName')[0];
                $this->ldap->bindUser($dn, $credentials['password']);
                $group = $this->entityManager->getRepository(Setting::class)->getValue("guestGroup");

                if ($group == null) {
                    $this->ldap->bind();
                    $ou = substr(strstr($dn, ',', false), 1);
                    $group = $this->entityManager->getRepository(Group::class)->findOneBy(['dn' => $ou]);
                    if ($group == null) {
                        $ou = $this->ldap->findOU($ou)->execute();
                        if (\Count($ou) == 1) {
                            $ou = $ou[0];
                            $group = new Group();
                            $group->setName($ou->getAttribute('name')[0])
                                    ->setDn($ou->getAttribute('distinguishedName')[0])
                                    ->setGroup($this->entityManager->getRepository(Setting::class)->getValue("adminGroup"))
                                    ->setPermissions(07, 04, 04);
                            $this->entityManager->persist($group);
                        }
                    }
                    if($group->getDn()==null){
                        
                    }
                }
                $user = new User();
                $user->setFirstName($result->getAttribute('givenName')[0])
                        ->setLastName($result->getAttribute('sn')[0])
                        ->setUsername($result->getAttribute('cn')[0])
                        ->setEmail($result->getAttribute('mail')[0] ?? "")
                        ->setPermissions(07, 04, 00)
                        ->setOwner($user)
                        ->setGroup($group);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } catch (ConnectionException $e) {
                throw new CustomUserMessageAuthenticationException('Contraseña invalida.');
            }
        }
        if ($user->getGroup() == $this->entityManager->getRepository(Setting::class)->getValue("guestGroup")) {
            throw new CustomUserMessageAuthenticationException('Usuario del Directorio Activo verificado. Favor de solicitar a un administrador de grupo añadirlo a un equipo.');
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user) {
        $valid = false;
        if ($user->getPassword() !== null) {
            $valid = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        } else {
            try {
                $this->ldap->bindUser($credentials['username'], $credentials['password']);
                $valid = true;
            } catch (ConnectionException $e) {
                $valid = false;
            }
        }
        return $valid;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('dashboard'));
    }

    protected function getLoginUrl() {
        return $this->urlGenerator->generate('login');
    }

}
