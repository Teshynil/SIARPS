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
        return $this->render('settings/index.html.twig', ['form' => $form->createView()
        ]);
    }

    public function action($action = null) {
        if ($action != null) {
            switch ($action) {
                case 'fillUsers':

                    $this->addFlash('success', "Proceso Completado|Se rellenaron correctamente los usuarios");
                    break;
            }
        }
        return $this->redirectToRoute('settings');
    }

}
