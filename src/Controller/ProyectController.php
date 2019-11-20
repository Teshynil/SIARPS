<?php

namespace App\Controller;

use App\Form\ProyectType;
use App\Security\PermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProyectController extends AbstractController {

    public function index() {

        
        return $this->render('proyect/index.html.twig', [
                    'proyects' => $ps
        ]);
    }

    public function new(PermissionService $ps, Request $request) {
        if (!$ps->hasWrite($this->getUser()->getGroup())) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(ProyectType::class,null,['em'=>$this->getDoctrine(),'usr'=>$this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            $this->getDoctrine()->getManager()->persist($data);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("proyects");
        }
        $formView=$form->createView();
        return $this->render('proyect/new.html.twig', [
                    'form' => $formView
        ]);
    }

}
