<?php

namespace App\Controller;

use App\Entity\Document;
use App\Helpers\SIARPSController;

class DocumentController extends SIARPSController {

     
    public function document($id){
        $document=$this->getDoctrine()->getManager()->find(Document::class, $id);
        if(!$this->getPermissionService()->hasRead($document)){
            throw $this->createAccessDeniedException();
        }
        return $this->render('project/document/document.html.twig', [
                    'document' => $document
        ]);
    }

    public function newVersion($id){
        $document=$this->getDoctrine()->getManager()->find(Document::class, $id);
        if(!$this->getPermissionService()->hasWrite($document)){
            throw $this->createAccessDeniedException();
        }
        return $this->render('project/document/document.html.twig', [
                    'document' => $document
        ]);
    }
    
}
