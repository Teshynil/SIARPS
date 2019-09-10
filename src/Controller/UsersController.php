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
        $users = $this->getDoctrine()->getManager()->getRepository(\App\Entity\User::class)->findAll();
        return $this->render('users/users.html.twig', [
                    'users' => $users
        ]);
    }

}
