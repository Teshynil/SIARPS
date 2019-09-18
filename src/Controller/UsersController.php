<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {

    public function user($id = null) {
        if ($id !== null) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
            if ($user == null) {
                $this->createNotFoundException("Usuario no encontrado");
            } else if ($user->isEqualTo($this->getUser())) {
                return $this->redirectToRoute('me');
            }
        } else {
            $user = $this->getUser();
        }
        return $this->render('users/user.html.twig', ['user' => $user]);
    }

    public function users() {
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();
        return $this->render('users/users.html.twig', [
                    'users' => $users
        ]);
    }

}
