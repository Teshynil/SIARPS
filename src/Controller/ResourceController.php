<?php

namespace App\Controller;

use App\Entity\File;
use App\Helpers\SIARPSController;
use App\Security\PermissionService;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ResourceController extends SIARPSController {

    public function serve($method = null, $id = null) {
        $file = $this->getDoctrine()->getManager()->find(File::class,$id);
        if ($file instanceof File) {
            if (!$this->getPermissionService()->hasRead($file)) {
                throw $this->createAccessDeniedException();
            }
            $file->prepareFile();
            $file->update();
            if ($file->isValid()) {
                if ($method == "view") {
                    return $this->file($file->getPath(), $file->getName().'.'.$file->getFile()->guessExtension(), ResponseHeaderBag::DISPOSITION_INLINE);
                } elseif ($method == "download") {
                    return $this->file($file->getPath(), $file->getName().'.'.$file->getFile()->guessExtension(), ResponseHeaderBag::DISPOSITION_ATTACHMENT);
                }
            } else {
                throw new ConflictHttpException("El archivo no concuerda con el almacenado");
            }
        } else {
            throw $this->createNotFoundException("Archivo no encontrado");
        }
    }

}
