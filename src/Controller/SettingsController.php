<?php

namespace App\Controller;

use App\Entity\Properties;
use App\Entity\Setting;
use App\Entity\User;
use App\Form\SettingsType;
use App\Helpers\SIARPSController;
use App\Security\FormAuthenticator;
use App\Security\Ldap;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends SIARPSController {

    /**
     * @Route("/settings", name="settings")
     */
    public function index(Request $request) {
        $form = $this->createForm(SettingsType::class);
        $originalData = $form->getData();
        $form->handleRequest($request);
        $settingRepo = $this->getDoctrine()->getRepository(Setting::class);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData() as $key => $value) {
                if ($originalData[$key] !== $value) {
                    $settingRepo->setValue($key, $value);
                }
            }
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->render('settings/index.html.twig', ['form' => $form->createView()
        ]);
    }

    public function action($action = null, Ldap $ldap, FormAuthenticator $auth) {
        if ($action != null) {
            switch ($action) {
                case 'fillUsers':
                    try {
                        $ldap->bind();
                    } catch (LdapException $ex) {
                        throw $ex;
                        $this->addFlash('error', "Error|" . $ex->getMessage());
                        break;
                    }
                    $users = $ldap->findAllUsers();
                    foreach ($users as $user) {
                        $result = $this->getDoctrine()->getRepository(User::class)->findByUsername($user->getAttribute('sAMAccountName')[0]);
                        if ($result == null) {
                            $auth->createUserFromLdap($user);
                        }
                    }
                    $this->addFlash('success', "Proceso Completado|Se rellenaron correctamente los usuarios");
                    break;
            }
        }
        return $this->redirectToRoute('settings');
    }

    public function lock($entity, $id) {
        try {
            $entity = $this->getDoctrine()->getManager()->find('App\\Entity\\' . $entity, $id);
            if ($entity instanceof Properties) {
                if (!$this->getPermissionService()->hasLock($entity)) {
                    $this->addFlash('error', "Error|No se poseen los permisos para realizar la accion");
                } else {
                    $entity->setLockState(true)->setLockedBy($this->getUser());
                    $this->getDoctrine()->getManager()->persist($entity);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', "Proceso Completado|Entidad Bloqueada");
                }
            } else {
                $this->addFlash('error', "Error|Entidad no encontrada");
            }
        } catch (Exception $ex) {
            $this->addFlash('error', "Error|Entidad no encontrada");
        }
        return new Response("OK");
    }

    public function unlock($entity, $id) {
        try {
            $entity = $this->getDoctrine()->getManager()->find('App\\Entity\\' . $entity, $id);
            if ($entity instanceof Properties) {
                if (!$this->getPermissionService()->hasLock($entity)) {
                    $this->addFlash('error', "Error|No se poseen los permisos para realizar la accion");
                } else {
                    $entity->setLockState(false)->setLockedBy(null);
                    $this->getDoctrine()->getManager()->persist($entity);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', "Proceso Completado|Entidad Desbloqueada");
                }
            } else {
                $this->addFlash('error', "Error|Entidad no encontrada");
            }
        } catch (Exception $ex) {
            $this->addFlash('error', "Error|Entidad no encontrada");
        }
        return new Response("OK");
    }
    
}
