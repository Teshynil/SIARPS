<?php

namespace App\Controller;

use App\Security\Ldap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DashboardController extends AbstractController {

    public function index(SessionInterface $session) {
        $session->set("notifications", [
            ["text" => "hola mundo",
                "icon" => "notebook",
                "color" => "success",
                "path" => "notifications",
                "parameters" => ["hola" => "dwa"]
            ]
        ]);
        return $this->render('dashboard.html.twig');
    }

}
