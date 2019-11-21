<?php

namespace App\Controller;

use App\Entity\Group;
use App\Helpers\SIARPSController;
use App\Security\PermissionService;

class ViewDebugController extends SIARPSController {

    public function index($page = "main") {
        return $this->render('view_debug/' . $page . ".html.twig");
    }

    public function test(PermissionService $ps) {
        $obj = $this->getDoctrine()->getRepository(Group::class)->find('0d0fc358-c3be-11e9-9785-e840f2eb9399');
        var_dump($ps->hasRead($obj));
        die();
    }

}
