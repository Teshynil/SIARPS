<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Helpers\SIARPSController;
use App\Security\PermissionService;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends SIARPSController {

    public function index() {
        
        $ps=$this->getDoctrine()->getRepository(Project::class)->getAvailableProjects($this->getUser());
        return $this->render('project/index.html.twig', [
                    'projects' => $ps
        ]);
    }
    
    public function project($id){
        $project=$this->getDoctrine()->getManager()->find(Project::class, $id);
        if(!$this->getPermissionService()->hasRead($project)){
            throw $this->createAccessDeniedException();
        }
        return $this->render('project/project_dashboard.html.twig', [
                    'project' => $project
        ]);
    }

    public function new(Request $request) {
        if (!$this->getPermissionService()->hasWrite($this->getUser()->getGroup())) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(ProjectType::class,null,['em'=>$this->getDoctrine(),'usr'=>$this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            $this->getDoctrine()->getManager()->persist($data);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("projects");
        }
        $formView=$form->createView();
        return $this->render('project/new.html.twig', [
                    'form' => $formView
        ]);
    }

}
