<?php

namespace App\Controller;

use App\Entity\Template;
use App\Form\CreateTemplateType;
use App\Form\EditTemplateType;
use App\Form\Requests\CreateTemplateRequest;
use App\Form\Requests\EditTemplateRequest;
use App\Form\TemplateViewType;
use App\Helpers\SIARPSController;
use Symfony\Component\HttpFoundation\Request;

class TemplateController extends SIARPSController {

    public function index() {

        $ts = $this->getDoctrine()->getRepository(Template::class)->getAvailableTemplates($this->getUser());
        return $this->render('template/index.html.twig', [
                    'templates' => $ts
        ]);
    }

    public function template($id) {
        $template = $this->getDoctrine()->getManager()->find(Template::class, $id);
        if (!$this->getPermissionService()->hasRead($template)) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('template/template_dashboard.html.twig', [
                    'template' => $template
        ]);
    }

    public function editFile(Request $request, $id) {
        $template = $this->getDoctrine()->getManager()->find(Template::class, $id);
        if (!$this->getPermissionService()->hasWrite($template)) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(TemplateViewType::class);
        $form->handleRequest($request);
        $data = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($form->get('updateView')->isClicked()) {
                
            } else {
                
            }
        } else {
            $form->get('template')->setData(<<<'EOD'
<div class="header">
    <img src="{{ asset('img/header.png') }}" style="height: 2.5cm">
    <p><strong><span>REPORTE DE AVANCES DE GESTIÓN</span></strong><br>
    <span><i>Seguimiento de Proyectos, Requerimientos y Servicios de la </i><strong>DGTID</strong></span></p>
</div>
<div class="footer">
    <p><strong><span>Revisó:  {{ document.lockedBy.fullName }}</span></strong></p>
</div>

<div class="page">
</div>
EOD);
        }
        $formView = $form->createView();
        return $this->render('template/editFile.html.twig', [
                    'template' => $template,
                    'data' => $data,
                    'form' => $formView
        ]);
    }

    public function edit($id, Request $request, EditTemplateRequest $editTemplateRequest) {
        $template = $this->getDoctrine()->getManager()->find(Template::class, $id);
        if (!$this->getPermissionService()->hasWrite($template)) {
            throw $this->createAccessDeniedException();
        }
        $editTemplateRequest->fillEntity($template);
        $locked = $this->getPermissionService()->hasLock($template);
        $form = $this->createForm(EditTemplateType::class, $editTemplateRequest, ['em' => $this->getDoctrine(), 'user' => $this->getUser(), 'locked' => $locked]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $template = $editTemplateRequest->createEntity();
            $this->getDoctrine()->getManager()->persist($template);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("templates");
        }
        $formView = $form->createView();
        return $this->render('template/edit.html.twig', [
                    'form' => $formView,
                    'template' => $template
        ]);
    }

    public function new(Request $request) {
        if (!$this->getPermissionService()->hasWrite($this->getUser()->getGroup())) {
            throw $this->createAccessDeniedException();
        }
        $template = new CreateTemplateRequest();
        $form = $this->createForm(CreateTemplateType::class, $template, ['em' => $this->getDoctrine(), 'user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $DBtemplate = $template->createEntity();
            $this->getDoctrine()->getManager()->persist($DBtemplate);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("template-view", ['id' => $DBtemplate->getId()]);
        }
        $formView = $form->createView();
        return $this->render('template/new.html.twig', [
                    'form' => $formView
        ]);
    }

}
