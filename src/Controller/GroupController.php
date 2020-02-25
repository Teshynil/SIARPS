<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Form\CreateGroupType;
use App\Form\CreateUserType;
use App\Form\EditGroupType;
use App\Form\EditUserType;
use App\Form\Requests\CreateGroupRequest;
use App\Form\Requests\CreateUserRequest;
use App\Form\Requests\EditGroupRequest;
use App\Form\Requests\EditUserRequest;
use App\Helpers\SIARPSController;
use Symfony\Component\HttpFoundation\Request;

class GroupController extends SIARPSController {

    public function new(Request $request) {
        if (!$this->getUser()->getAdminMode()) {
            throw $this->createAccessDeniedException();
        }
        $data = new CreateGroupRequest();
        $form = $this->createForm(CreateGroupType::class, $data, ['em' => $this->getDoctrine(), 'user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group = $data->createEntity();
            $this->getDoctrine()->getManager()->persist($group);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Grupo Creado|El grupo " . $group->getName() . " fue creado correctamente");
            return $this->redirectToRoute("groups");
        }
        $formView = $form->createView();
        return $this->render('groups/new.html.twig', [
                    'form' => $formView
        ]);
    }

    public function edit($id, Request $request) {
        $group = $this->getDoctrine()->getManager()->find(Group::class, $id);
        if (!$this->getPermissionService()->hasWrite($group)) {
            throw $this->createAccessDeniedException();
        }
        $locked = $this->getPermissionService()->hasLock($group);
        $groupEdit = new EditGroupRequest();
        $groupEdit->fillEntity($group);
        $form = $this->createForm(EditGroupType::class, $groupEdit, ['em' => $this->getDoctrine(), 'user' => $this->getUser(), 'locked' => $locked]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $DBGroup = $groupEdit->createEntity();
            $this->getDoctrine()->getManager()->persist($DBGroup);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Grupo Editado|El Grupo " . $DBGroup->getName() . " fue editado correctamente");
            return $this->redirectToRoute("group", ['id' => $DBGroup->getId()]);
        }
        $formView = $form->createView();
        return $this->render('groups/edit.html.twig', [
                    'group' => $group,
                    'form' => $formView
        ]);
    }

    public function group($id = null) {
        $group = $this->getDoctrine()->getManager()->find(Group::class, $id);
        if (!$this->getPermissionService()->hasRead($group)) {
            throw $this->createAccessDeniedException();
        }
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findByGroup($group);
        return $this->render('groups/group.html.twig', ['group' => $group, 'users' => $users]);
    }

    public function groups() {
        $groups = $this->getDoctrine()->getManager()->getRepository(Group::class)->findAll();
        return $this->render('groups/groups.html.twig', [
                    'groups' => $groups
        ]);
    }

}
