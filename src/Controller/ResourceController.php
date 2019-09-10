<?php

namespace App\Controller;

use App\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController {

    public function serve($method = null, $id = null) {
        $file = $this->getDoctrine()->getManager()->getRepository(File::class)->find($id);
        if ($file instanceof File) {
            $file->prepareFile();
            $file->update();
            if ($file->isValid()) {
                if ($method == "view") {
                    return $this->file($file->getPath(), $file->getName(), ResponseHeaderBag::DISPOSITION_INLINE);
                } elseif ($method == "download") {
                    return $this->file($file->getPath(), $file->getName(), ResponseHeaderBag::DISPOSITION_ATTACHMENT);
                }
            } else {
                throw new ConflictHttpException("El archivo no concuerda con el almacenado");
            }
        } else {
            throw $this->createNotFoundException("Archivo no encontrado");
        }
    }

}
