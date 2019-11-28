<?php

namespace App\Controller;

use App\Entity\Template;
use App\Form\TemplateType;
use App\Helpers\SIARPSController;
use Symfony\Component\HttpFoundation\Request;

class TemplateController extends SIARPSController
{
    
    public function index() {
        
        $ts=$this->getDoctrine()->getRepository(Template::class)->getAvailableTemplates($this->getUser());
        return $this->render('template/index.html.twig', [
                    'templates' => $ts
        ]);
    }
    
    public function template($id){
        $template=$this->getDoctrine()->getManager()->find(Template::class, $id);
        if(!$this->getPermissionService()->hasRead($template)){
            throw $this->createAccessDeniedException();
        }
        return $this->render('template/template_dashboard.html.twig', [
                    'template' => $template
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
            $templateForm=$form->get('templateForm')->getData();
            if($data->getType()=='Form'){
                $data->setSetting('fields',$templateForm);
            }
            $this->getDoctrine()->getManager()->persist($data);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("templates");
        }
        $formView=$form->createView();
        return $this->render('template/new.html.twig', [
                    'form' => $formView
        ]);
    }
}
