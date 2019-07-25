<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {

    public function index() {
        return $this->render('users/index.html.twig', [
                    'controller_name' => 'UsersController',
        ]);
    }

    public function user($id = null) {
        return $this->render('users/user.html.twig', [
        ]);
    }

}
