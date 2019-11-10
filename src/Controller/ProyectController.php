<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProyectController extends AbstractController {

    /**
     * @Route("/proyect", name="proyect")
     */
    public function index() {
        
        $ps = [
            ['id' => 12038102,'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'),strtotime('01/06/2019'))), 'progress' => rand(0, 100)],
            ['id' => 12038102,'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'),strtotime('01/06/2019'))), 'progress' => rand(0, 100)],
            ['id' => 12038102,'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'),strtotime('01/06/2019'))), 'progress' => rand(0, 100)],
            ['id' => 12038102,'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'),strtotime('01/06/2019'))), 'progress' => rand(0, 100)],
            ['id' => 12038102,'name' => 'test', 'lastUpdate' => date_create(rand(strtotime('01/01/2018'),strtotime('01/06/2019'))), 'progress' => rand(0, 100)]
        ];
        return $this->render('proyect/index.html.twig', [
                    'proyects' => $ps
        ]);
    }

}
