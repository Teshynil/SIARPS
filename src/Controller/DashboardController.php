<?php

namespace App\Controller;

use App\Security\Ldap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DashboardController extends AbstractController {

    public function index(Request $request) {
        return $this->render('dashboard.html.twig');
    }

}
