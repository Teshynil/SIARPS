<?php

namespace App\Security;

use App\Entity\Group;
use App\Entity\Notification;
use App\Entity\Setting;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class FormAuthenticator extends AbstractFormLoginAuthenticator {

    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $ldap;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $ldapResult;

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
        try {
            $token = new CsrfToken('authenticate', $credentials['csrf_token']);
            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
            $user = null;
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);
            if (!$user) {
                $this->ldap->bind();
                $result = $this->ldap->findUserQuery($credentials['username']);
                $count = \count($result);
                if (!$count) {
                    throw new CustomUserMessageAuthenticationException('No se pudo encontrar al usuario');
                }
                if ($count > 1) {
                    throw new CustomUserMessageAuthenticationException('Multiples usuarios encontrados en el Directorio Activo');
                }
                /** @var Entry * */
                $result = $result[0];
                $this->ldapResult = $result;
                $user = $this->createUserFromLdap($result);
            }
            
            if ($user->getPassword() == null) {
                if ($user->getGroup() == $this->entityManager->getRepository(Setting::class)->getValue("guestGroup")) {
                    throw new CustomUserMessageAuthenticationException('Usuario del Directorio Activo verificado. Favor de solicitar a un administrador de grupo añadirlo a un equipo.');
                }
                $this->changeLdapGroup($user);
                $this->verifyLdapOwner($user);
            }

            return $user;
        } catch (LdapConnectionTimeout $exc) {
            throw new CustomUserMessageAuthenticationException('No se tiene acceso al Directorio Activo.');
        }
    }

    public function createUserFromLdap($ldapUser): User {
        $user = new User();
        $dn = $ldapUser->getAttribute('distinguishedName')[0];
        $group = $this->entityManager->getRepository(Setting::class)->getValue("guestGroup");
        $user->setFirstName($ldapUser->getAttribute('givenName')[0])
                ->setLastName($ldapUser->getAttribute('sn')[0])
                ->setUsername($ldapUser->getAttribute('sAMAccountName')[0])
                ->setDn($dn)
                ->setEmail($ldapUser->getAttribute('mail')[0] ?? null)
                ->setPermissions(07, 04, 00)
                ->setOwner($user);
        if ($group == null) {
            $group = $this->getGroupFromLdap($user);
        }
        $user->setGroup($group);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function verifyLdapOwner($user) {
        try {
            if ($user->getGroup()->getDn() !== null && $user->getGroup()->getOwner() == null) {
                $this->ldap->bind();
                $ldapOwner = $this->ldap->findGroupOwner($user->getGroup()->getDn());
                $count = \Count($ldapOwner);
                if (!$count) {
                    throw new CustomUserMessageAuthenticationException('Tu usuario pertenece a un grupo sin administrador en el Active Directory, una vez exista y acceda al sistema el administrador del grupo se desbloqueara el acceso.');
                }
                $ldapOwner = $ldapOwner[0];
                $owner = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $ldapOwner->getAttribute($this->ldap->getLdapParams()["SIARPS_LOGIN_ATTRIBUTE"])]);
                if ($owner == null) {
                    throw new CustomUserMessageAuthenticationException('Tu usuario pertenece a un grupo en el que el administrador no a accedido al sistema, una vez acceda, se desbloqueara el acceso.');
                } else {
                    if ($owner == $user) {
                        $user->getGroup()->setOwner($user);
                        $this->entityManager->flush();
                    }
                }
            }
        } catch (LdapConnectionTimeout $exc) {
            throw new CustomUserMessageAuthenticationException('No se tiene acceso al Directorio Activo.');
        }
    }

    public function changeLdapGroup($user) {
        $this->ldap->bind();
        $usg = $user->getGroup();
        $utg = $this->getGroupFromLdap($user);
        if ($usg !== $utg) {
            if ($usg->getOwner() == $user) {
                $usg->setOwner(null);
            }
            $user->setGroup($utg);
            $user->addNotification(new Notification("Tu usuario ha sido cambiado de grupo en el Directorio Activo", Notification::COLOR_RED));
            $this->entityManager->flush();
        }
    }

    public function getGroupFromLdap(User $user = null): ?Group {
        if ($user != null) {
            $this->ldapResult = $this->ldap->findUserQuery($user->getUsername());
            $count = \count($this->ldapResult);
            if (!$count) {
                throw new CustomUserMessageAuthenticationException('No se pudo encontrar al usuario');
            }
            if ($count > 1) {
                throw new CustomUserMessageAuthenticationException('Multiples usuarios encontrados en el Directorio Activo');
            }
            $this->ldapResult = $this->ldapResult[0];
        }

        if ($this->getLdapUserType($this->ldapResult) == Ldap::ADMIN) {
            return $this->entityManager->getRepository(Setting::class)->getValue("adminGroup");
        } else {
            $udn = $this->ldapResult->getAttribute('distinguishedName')[0];
            $gdn = substr(strstr($udn, ',', false), 1);
            $group = $this->entityManager->getRepository(Group::class)->findOneBy(['dn' => $gdn]);
            if ($group == null) {
                $ldapGroup = $this->ldap->findOU($gdn);
                if (\Count($ldapGroup) == 1) {
                    $ldapGroup = $ldapGroup[0];
                    $group = new Group();
                    $group->setName($ldapGroup->getAttribute('name')[0])
                            ->setDn($ldapGroup->getAttribute('distinguishedName')[0])
                            ->setGroup($this->entityManager->getRepository(Setting::class)->getValue("adminGroup"))
                            ->setPermissions(07, 04, 04);
                    $this->entityManager->persist($group);
                    $this->entityManager->flush();
                }
            }
        }
        return $group;
    }

    public function checkCredentials($credentials, UserInterface $user) {
        $valid = false;
        if ($user->getPassword() !== null) {
            $valid = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        } else {
            try {
                $this->ldap->bindUser($user->getDn(), $credentials['password']);
                $valid = true;
            } catch (ConnectionException $e) {
                $valid = false;
            }
        }
        return $valid;
    }

    public function getLdapUserType($ldapUser) {
        $groups = $ldapUser->getAttribute('memberOf');
        $type = 0;
        foreach ($groups as $group) {
            if ($group == $this->ldap->getLdapParams()->SIARPS_LDAP_ADMIN_GROUP_DN) {
                $type |= Ldap::ADMIN;
            } else if ($group == $this->ldap->getLdapParams()->SIARPS_LDAP_USER_GROUP_DN) {
                $type |= Ldap::USER;
            }
        }
        return $type;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        $user = $token->getUser();
        if ($user->getPassword() == null) {
            if ($this->ldapResult == null) {
                $this->ldapResult = $this->ldap->findUserQuery($token->getUsername());
                $this->ldapResult = $this->ldapResult[0];
            }
            if (($this->getLdapUserType($this->ldapResult) & Ldap::ADMIN) > 0) {
                $request->getSession()->set("selectLoginMode", true);
                return new RedirectResponse($this->urlGenerator->generate('selectLoginMode'));
            }
        } else if ($user->getGroup() == $this->entityManager->getRepository(Setting::class)->getValue("adminGroup")) {
            $request->getSession()->set("selectLoginMode", true);
            return new RedirectResponse($this->urlGenerator->generate('selectLoginMode'));
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('dashboard'));
    }

    protected function getLoginUrl() {
        return $this->urlGenerator->generate('login');
    }

}
