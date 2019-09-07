<?php

namespace App\Controller;

use App\Security\Ldap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DashboardController extends AbstractController {

    public function index() {
        return $this->render('dashboard.html.twig');
    }

}
