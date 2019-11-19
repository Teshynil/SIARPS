<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SettingsType;
use App\Security\FormAuthenticator;
use App\Security\Ldap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController {

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
                    $ldap->bind();
                    $users = $ldap->findAllUsers();
                    foreach ($users as $user) {
                        $result=$this->getDoctrine()->getRepository(\App\Entity\User::class)->findByUsername($user->getAttribute('sAMAccountName')[0]);
                        if($result==null){
                            $auth->createUserFromLdap($user);
                        }
                    }
                    $this->addFlash('success', "Proceso Completado|Se rellenaron correctamente los usuarios");
                    break;
            }
        }
        return $this->redirectToRoute('settings');
    }

}
