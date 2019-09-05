<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {

    public function user($id = null) {
        return $this->render('users/user.html.twig', [
        ]);
    }

    public function users() {
        return $this->render('users/users.html.twig', [
        ]);
    }

}
