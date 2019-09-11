<?php

namespace App\Controller;

use App\Security\PermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ViewDebugController extends AbstractController {

    public function index($page = "main") {
        return $this->render('view_debug/' . $page . ".html.twig");
    }

    public function test(PermissionService $ps) {
        $obj = $this->getDoctrine()->getRepository(\App\Entity\Group::class)->find('0d0fc358-c3be-11e9-9785-e840f2eb9399');
        var_dump($ps->hasRead($obj));
        die();
    }

}
