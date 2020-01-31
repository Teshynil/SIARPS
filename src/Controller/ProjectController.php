<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Project;
use App\Entity\Setting;
use App\Entity\Template;
use App\Form\CreateProjectType;
use App\Form\EditProjectType;
use App\Form\Requests\CreateProjectRequest;
use App\Form\Requests\EditProjectRequest;
use App\Helpers\SIARPSController;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends SIARPSController {

    public function index() {

        $ps = $this->getDoctrine()->getRepository(Project::class)->getAvailableProjects($this->getUser());
        return $this->render('project/index.html.twig', [
                    'projects' => $ps
        ]);
    }

    public function project($id) {
        $project = $this->getDoctrine()->getManager()->find(Project::class, $id);
        if (!$this->getPermissionService()->hasRead($project)) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('project/project_dashboard.html.twig', [
                    'project' => $project
        ]);
    }

    public function edit(Request $request, $id,EditProjectRequest $editProjectRequest) {
        $project = $this->getDoctrine()->getManager()->find(Project::class, $id);
        if (!$this->getPermissionService()->hasWrite($project)) {
            throw $this->createAccessDeniedException();
        }
        $editProjectRequest->fillEntity($project);
        $locked = $this->getPermissionService()->hasLock($project);
        $form = $this->createForm(EditProjectType::class, $editProjectRequest, ['em' => $this->getDoctrine(), 'user' => $this->getUser(),'project'=>$project, 'locked' => $locked]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $template = $editProjectRequest->createEntity();
            $this->getDoctrine()->getManager()->persist($template);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("project",['id'=>$id]);
        }
        $formView = $form->createView();
        return $this->render('project/edit.html.twig', [
                    'form' => $formView,
                    'project' => $project
        ]);
    }

    public function new(Request $request) {
        if (!$this->getPermissionService()->hasWrite($this->getUser()->getGroup())) {
            throw $this->createAccessDeniedException();
        }
        $data = new CreateProjectRequest();
        $form = $this->createForm(CreateProjectType::class, $data, ['em' => $this->getDoctrine(), 'user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $data->createEntity();
            $defaultTemplate = $this->getDoctrine()->getRepository(Setting::class)->getValue('requiredTemplate');
            if ($defaultTemplate instanceof Template) {
                $defaultDocument = new Document();
                $defaultDocument->setName("Resumen de Avance")
                        ->setOwner($project->getOwner())
                        ->setGroup($project->getGroup())
                        ->setPermissions($project->getOwnerPermissions(), $project->getGroupPermissions(), $project->getOtherPermissions())
                        ->setTemplate($defaultTemplate);
                $project->addDocument($defaultDocument);
                $project->setSummary($defaultDocument);
            }
            $this->getDoctrine()->getManager()->persist($project);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("projects");
        }
        $formView = $form->createView();
        return $this->render('project/new.html.twig', [
                    'form' => $formView
        ]);
    }

}
