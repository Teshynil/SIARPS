<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProyectController extends AbstractController
{
    /**
     * @Route("/proyect", name="proyect")
     */
    public function index()
    {
        return $this->render('proyect/index.html.twig', [
            'controller_name' => 'ProyectController',
        ]);
    }
}
