<?php

namespace App\Controller;

use App\Form\SettingsType;
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
        $settingRepo = $this->getDoctrine()->getRepository(\App\Entity\Setting::class);
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

    public function action($action = null, \App\Security\Ldap $ldap) {
        if ($action != null) {
            switch ($action) {
                case 'fillUsers':
                    $users = $ldap->findAllUsers();
                    $this->addFlash('success', "Proceso Completado|Se rellenaron correctamente los usuarios");
                    break;
            }
        }
        return $this->redirectToRoute('settings');
    }

}
