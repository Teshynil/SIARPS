<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use App\Form\EditUserType;
use App\Form\Requests\CreateUserRequest;
use App\Form\Requests\EditUserRequest;
use App\Helpers\SIARPSController;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends SIARPSController {

    public function new(Request $request,CreateUserRequest $user) {
        if (!$this->getPermissionService()->hasWrite($this->getUser()->getGroup())) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(CreateUserType::class, $user, ['em' => $this->getDoctrine(), 'user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $DBUser = $user->createEntity();
            $this->getDoctrine()->getManager()->persist($DBUser);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Usuario Creado|El usuario " . $DBUser->getUsername() . " fue creado correctamente");
            return $this->redirectToRoute("users");
        }
        $formView = $form->createView();
        return $this->render('users/new.html.twig', [
                    'form' => $formView
        ]);
    }
    
    public function edit($id, Request $request,EditUserRequest $userEdit) {
        $user = $this->getDoctrine()->getManager()->find(User::class, $id);
        if (!$this->getPermissionService()->hasWrite($user)) {
            throw $this->createAccessDeniedException();
        }
        $locked=$this->getPermissionService()->hasLock($user);
        $userEdit->fillEntity($user);
        $form = $this->createForm(EditUserType::class, $userEdit, ['em' => $this->getDoctrine(), 'user' => $this->getUser(),'locked'=>$locked]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $DBUser = $userEdit->createEntity();
            $this->getDoctrine()->getManager()->persist($DBUser);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Usuario Editado|El usuario " . $DBUser->getUsername() . " fue editado correctamente");
            return $this->redirectToRoute("user",['id'=>$DBUser->getId()]);
        }
        $formView = $form->createView();
        return $this->render('users/edit.html.twig', [
                    'form' => $formView
        ]);
    }

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
