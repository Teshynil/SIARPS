<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ViewDebugController extends AbstractController {

    public function index($page = "main") {
        return $this->render('view_debug/' . $page . ".html.twig");
    }

}
