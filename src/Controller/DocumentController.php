<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Template;
use App\Entity\Version;
use App\Form\CreateVersionType;
use App\Form\Requests\CreateVersionRequest;
use App\Helpers\FormFieldResolver;
use App\Helpers\SIARPSController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;

class DocumentController extends SIARPSController {

    public function document($id) {
        $document = $this->getDoctrine()->getManager()->find(Document::class, $id);
        if (!$this->getPermissionService()->hasRead($document)) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('project/document/document.html.twig', [
                    'document' => $document
        ]);
    }

    public function newVersion($id, Request $request, CreateVersionRequest $data) {
        $document = $this->getDoctrine()->getManager()->find(Document::class, $id);
        if (!$this->getPermissionService()->hasWrite($document)) {
            throw $this->createAccessDeniedException();
        }
        $hasLock = true;
        foreach ($document->getVersions() as $version) {
            if (!$version->getLockState()) {
                if (!$this->getPermissionService()->hasLock($version)) {
                    $hasLock = false;
                    break;
                }
            }
        }
        if ($hasLock == false) {
            $this->addFlash('error', "No se pudo completar|Todas las versiones anteriores deben estar bloqueadas antes de crear una nueva.");
            return $this->render('project/document/document.html.twig', [
                        'document' => $document
            ]);
        } else {
            $form = $this->createForm(CreateVersionType::class, $data, ['em' => $this->getDoctrine(), 'user' => $this->getUser()]);
            $formFields = $this->createFormBuilder()->create('fields', FormType::class, [
                'label' => 'Campos',
                'auto_initialize' => false,
            ]);
            $template = $document->getTemplate();
            if ($template instanceof Template) {
                if ($template->getType() == 'Form') {
                    $fields = $template->getSetting('fields');
                    foreach ($fields as $field) {
                        $formFields->add(FormFieldResolver::resolveFieldToForm($field, $formFields));
                    }
                } else {
                    $form->add('file', FileType::class);
                }
            }
            $form->add($formFields->getForm());
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $version = $data->createEntity();
                foreach ($document->getVersions() as $prevVersion) {
                    if (!$prevVersion->getLockState()) {
                        if ($this->getPermissionService()->hasLock($prevVersion)) {
                            $prevVersion->setLockState(true);
                            $prevVersion->setLockedBy($this->getUser());
                            $this->getDoctrine()->getManager()->persist($prevVersion);
                        }
                    }
                }
                $version->setDocument($document);
                $fields = array_merge(['fields' => $data->fields], ['page' => null], ['template' => null]);
                $this->getDoctrine()->getManager()->persist($version);
                $version->fillFile($fields);
                $this->getDoctrine()->getManager()->persist($version);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('document', ['id' => $id]);
            }
        }
        return $this->render('project/document/newVersion.html.twig', [
                    'document' => $document,
                    'form' => $form->createView()
        ]);
    }

    public function editVersion($id, Request $request, CreateVersionRequest $data) {
        $document = $this->getDoctrine()->getManager()->find(Document::class, $id);
        if (!$this->getPermissionService()->hasWrite($document)) {
            throw $this->createAccessDeniedException();
        }
        $hasLock = true;
        foreach ($document->getVersions() as $version) {
            if (!$version->getLockState()) {
                if (!$this->getPermissionService()->hasLock($version)) {
                    $hasLock = false;
                    break;
                }
            }
        }
        if ($hasLock == false) {
            $this->addFlash('error', "No se pudo completar|Todas las versiones anteriores deben estar bloqueadas antes de crear una nueva.");
            return $this->render('project/document/document.html.twig', [
                        'document' => $document
            ]);
        } else {
            $form = $this->createForm(CreateVersionType::class, $data, ['em' => $this->getDoctrine(), 'user' => $this->getUser()]);
            $formFields = $this->createFormBuilder()->create('fields', FormType::class, [
                'label' => 'Campos',
                'auto_initialize' => false,
            ]);
            $template = $document->getTemplate();
            if ($template instanceof Template) {
                if ($template->getType() == 'Form') {
                    $fields = $template->getSetting('fields');
                    foreach ($fields as $field) {
                        $formFields->add(FormFieldResolver::resolveFieldToForm($field, $formFields));
                    }
                } else {
                    $form->add('file', FileType::class);
                }
            }
            $form->add($formFields->getForm());
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $version = $data->createEntity();
                foreach ($document->getVersions() as $prevVersion) {
                    if (!$prevVersion->getLockState()) {
                        if ($this->getPermissionService()->hasLock($prevVersion)) {
                            $prevVersion->setLockState(true);
                            $prevVersion->setLockedBy($this->getUser());
                            $this->getDoctrine()->getManager()->persist($prevVersion);
                        }
                    }
                }
                $version->setDocument($document);
                $fields = array_merge(['fields' => $data->fields], ['page' => null], ['template' => null]);
                $this->getDoctrine()->getManager()->persist($version);
                $version->fillFile($fields);
                $this->getDoctrine()->getManager()->persist($version);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('document', ['id' => $id]);
            }
        }
        return $this->render('project/document/newVersion.html.twig', [
                    'document' => $document,
                    'form' => $form->createView()
        ]);
    }

    public function viewVersion($id) {
        $document = $this->getDoctrine()->getManager()->find(Version::class, $id);
        if (!$this->getPermissionService()->hasRead($document)) {
            throw $this->createAccessDeniedException();
        }
        if ($document instanceof Version) {

            $data = $this->getDoctrine()->getManager()->getRepository(Version::class)->getData($document);
            if ($document->getLockState() == true) {
                if ($data['page'] == null || $data['template'] == null) {
                    $template = $document->getDocument()->getTemplate()->getFile()->readFile("JSON");
                    $page = $document->getDocument()->getTemplate()->getSetting('page');
                } else {
                    $template = json_decode($data['template']);
                    $page = $data['page'];
                }
            } else {
                $template = $document->getDocument()->getTemplate()->getFile()->readFile("JSON");
                $page = $document->getDocument()->getTemplate()->getSetting('page');
            }
            return $this->render('project/document/viewDocument.html.twig', [
                        'entity' => $document,
                        'document' => $data['fields'],
                        'project' => $document->getDocument()->getProject(),
                        'template' => $template,
                        'page' => $page
            ]);
        }
    }

    public function printVersion($id) {
        $document = $this->getDoctrine()->getManager()->find(Version::class, $id);
        if (!$this->getPermissionService()->hasRead($document)) {
            throw $this->createAccessDeniedException();
        }
        if ($document instanceof Version) {

            $data = $this->getDoctrine()->getManager()->getRepository(Version::class)->getData($document);
            if ($document->getLockState() == true) {
                if ($data['page'] == null || $data['template'] == null) {
                    $template = $document->getDocument()->getTemplate()->getFile()->readFile("JSON");
                    $page = $document->getDocument()->getTemplate()->getSetting('page');
                } else {
                    $template = json_decode($data['template']);
                    $page = $data['page'];
                }
            } else {
                $template = $document->getDocument()->getTemplate()->getFile()->readFile("JSON");
                $page = $document->getDocument()->getTemplate()->getSetting('page');
            }
            return $this->render('core/document_base.html.twig', [
                        'document' => $data['fields'],
                        'project' => $document->getDocument()->getProject(),
                        'template' => $template,
                        'page' => $page
            ]);
        }
    }

}
