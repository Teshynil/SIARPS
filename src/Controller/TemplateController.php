<?php

namespace App\Controller;

use App\Entity\Template;
use App\Form\TemplateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TemplateController extends AbstractController
{
    
    public function index() {
        
        $ts=$this->getDoctrine()->getRepository(Template::class)->getAvailableTemplates($this->getUser());
        return $this->render('template/index.html.twig', [
                    'templates' => $ts
        ]);
    }
    
    public function template($id){
        $project=$this->getDoctrine()->getManager()->find(Template::class, $id);
        if(!$this->getPermissionService()->hasRead($project)){
            throw $this->createAccessDeniedException();
        }
        return $this->render('template/template_dashboard.html.twig', [
                    'template' => $project
        ]);
    }

    public function new(Request $request) {
        if (!$this->getPermissionService()->hasWrite($this->getUser()->getGroup())) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(TemplateType::class,null,['em'=>$this->getDoctrine(),'usr'=>$this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            $this->getDoctrine()->getManager()->persist($data);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("projects");
        }
        $formView=$form->createView();
        return $this->render('template/new.html.twig', [
                    'form' => $formView
        ]);
    }
}
