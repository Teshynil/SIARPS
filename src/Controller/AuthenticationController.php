<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SelectLoginModeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController {

    public function login(AuthenticationUtils $authenticationUtils): Response {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('authentication/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function selectLoginMode(Request $request, TokenStorageInterface $token_storage): Response {
        if ($request->getSession()->get("selectLoginMode", false)) {
            $token = $token_storage->getToken();
            $form = $this->createForm(SelectLoginModeType::class);
            $form->handleRequest($request);
            if ($this->getUser()->getGroup() == $this->getDoctrine()->getRepository(Setting::class)->getValue("adminGroup")) {
                $this->getUser()->setAdminMode(true);
                $newToken = new PostAuthenticationGuardToken($this->getUser(), $token->getProviderKey(), $token->getRoleNames());
                $token_storage->setToken($newToken);
                return $this->redirectToRoute("dashboard");
            }
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('modoUsuario')->isClicked()) {
                    $this->getUser()->setAdminMode(false);
                } else if ($form->get('modoAdministrador')->isClicked()) {
                    $this->getUser()->setAdminMode(true);
                } else {
                    return $this->render('authentication/select_login_mode.html.twig', ['form' => $form->createView()]);
                }
                $request->getSession()->remove("selectLoginMode");
                $newToken = new PostAuthenticationGuardToken($this->getUser(), $token->getProviderKey(), $token->getRoleNames());
                $token_storage->setToken($newToken);
                return $this->redirectToRoute("dashboard");
            }

            return $this->render('authentication/select_login_mode.html.twig', ['form' => $form->createView()]);
        } else {
            return $this->redirectToRoute("dashboard");
        }
    }

}
