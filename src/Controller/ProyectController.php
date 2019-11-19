<?php

namespace App\Controller;

use App\Form\ProyectType;
use App\Security\PermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProyectController extends AbstractController {

    public function index() {

        $ps = [
            ['id' => 12038102, 'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'), strtotime('01/06/2019'))), 'progress' => rand(0, 100)],
            ['id' => 12038102, 'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'), strtotime('01/06/2019'))), 'progress' => rand(0, 100)],
            ['id' => 12038102, 'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'), strtotime('01/06/2019'))), 'progress' => rand(0, 100)],
            ['id' => 12038102, 'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'), strtotime('01/06/2019'))), 'progress' => rand(0, 100)],
            ['id' => 12038102, 'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'), strtotime('01/06/2019'))), 'progress' => rand(0, 100)]
        ];
        return $this->render('proyect/index.html.twig', [
                    'proyects' => $ps
        ]);
    }

    public function new(PermissionService $ps, Request $request) {
        if (!$ps->hasWrite($this->getUser()->getGroup())) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(ProyectType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            var_dump($data);
            die();
        }
        $formView=$form->createView();
        return $this->render('proyect/new.html.twig', [
                    'form' => $formView
        ]);
    }

}
