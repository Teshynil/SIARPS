<?php

namespace App\Controller;

use App\Entity\Template;
use App\Form\CreateTemplateType;
use App\Form\EditTemplateType;
use App\Form\Requests\CreateTemplateRequest;
use App\Form\Requests\EditTemplateRequest;
use App\Form\Requests\EditTemplateViewRequest;
use App\Form\TemplateViewType;
use App\Helpers\SIARPSController;
use App\Helpers\WordToHtmlHelper;
use Symfony\Component\HttpFoundation\Request;

class TemplateController extends SIARPSController {

    public function index() {

        $ts = $this->getDoctrine()->getRepository(Template::class)->getAvailableTemplates($this->getUser());
        return $this->render('template/templates.html.twig', [
                    'templates' => $ts
        ]);
    }

    public function template($id) {
        $template = $this->getDoctrine()->getManager()->find(Template::class, $id);
        if (!$this->getPermissionService()->hasRead($template)) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('template/template.html.twig', [
                    'template' => $template
        ]);
    }

    public function editFile(Request $request, $id) {
        $template = $this->getDoctrine()->getManager()->find(Template::class, $id);
        if (!$this->getPermissionService()->hasWrite($template)) {
            throw $this->createAccessDeniedException();
        }
        $editTemplateView = new EditTemplateViewRequest();
        $editTemplateView->fillEntity($template);
        $form = $this->createForm(TemplateViewType::class, $editTemplateView);
        $form->handleRequest($request);
        $page = $form->getData()->getSettings();
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('template')->get('templateFromWord')->getData();
            if ($file !== null) {
                $zip = zip_open($file->getPathname());
                $indexHtml = false;
                $headerHtml = null;
                $images = [];
                while ($zipfile = zip_read($zip)) {
                    $ext = pathinfo(zip_entry_name($zipfile), PATHINFO_EXTENSION);
                    $name = pathinfo(zip_entry_name($zipfile), PATHINFO_FILENAME);
                    $fileBin = zip_entry_read($zipfile,zip_entry_filesize($zipfile));
                    if (substr($ext, 0, strlen('htm')) == 'htm' && !$indexHtml) {
                        $indexHtml = utf8_encode($fileBin);
                        
                    } else if ($name == "header") {
                        $headerHtml = utf8_encode($fileBin);
                    } else if (in_array($ext, ['jpg', 'jpeg', 'png', 'bmp'])) {
                        $f = finfo_open();
                        $mime_type = finfo_buffer($f, $fileBin, FILEINFO_MIME_TYPE);
                        $images[$name . '.' . $ext] = 'data:' . $mime_type . ';base64,' . base64_encode($fileBin);
                    }
                }
                $editTemplateView->setTemplateBody($indexHtml);
                $editTemplateView->setTemplateExternal($headerHtml);
                $editTemplateView->templateBody=WordToHtmlHelper::replaceImages($images,$editTemplateView->templateBody);
                $editTemplateView->templateExternal=WordToHtmlHelper::replaceImages($images,$editTemplateView->templateExternal);
            }
            $data = $editTemplateView->createEntity();
            $this->getDoctrine()->getManager()->persist($data);
            $this->getDoctrine()->getManager()->flush();
            if ($form->get('updateView')->isClicked()) {
                return $this->redirectToRoute('template-view', ['id' => $id]);
            } else {
                return $this->redirectToRoute('template', ['id' => $id]);
            }
        } else {
            if (empty($form->get('template')->get('templateExternal')->getData())) {
                $form->get('template')->get('templateExternal')->setData(<<<'EOD'
<div class="header">
</div>
<div class="footer">
</div>
EOD);
            }
            if (empty($form->get('template')->get('templateBody')->getData())) {
                $form->get('template')->get('templateBody')->setData(<<<'EOD'
<div class="page">
</div>
EOD);
            }
        }
        $formView = $form->createView();
        return $this->render('template/editFile.html.twig', [
                    'template' => $template,
                    'form' => $formView,
                    'page' => $page
        ]);
    }

    public function edit($id, Request $request, EditTemplateRequest $editTemplateRequest) {
        $template = $this->getDoctrine()->getManager()->find(Template::class, $id);
        if (!$this->getPermissionService()->hasWrite($template)) {
            throw $this->createAccessDeniedException();
        }
        $editTemplateRequest->fillEntity($template);
        $locked = $this->getPermissionService()->hasLock($template);
        $form = $this->createForm(EditTemplateType::class, $editTemplateRequest, ['em' => $this->getDoctrine(), 'user' => $this->getUser(), 'locked' => $locked,'groupNullable' => true]);
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
        $form = $this->createForm(CreateTemplateType::class, $template, ['em' => $this->getDoctrine(), 'user' => $this->getUser(),'groupNullable' => true]);
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
