<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        if($this->getUser()){
            return $this->redirectToRoute('homepage');
        }
        
        $loginform = $this->createForm(LoginType::class);

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'loginform' => $loginform->createView()
            ]
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutCheck()
    {
        // This code is never executed.
    }
}
