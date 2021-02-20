<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    // private property used for encoding passwords
    private $passwordEncoder;

    // constructor function for injecting the password encoder interface
    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {

        $this->passwordEncoder = $passwordEncoder;
    }
    

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Render the register template
     * 
     * @Route("/register", methods={"get"}, name="app_register")
     */
    public function register(): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        return $this->render('security/register.html.twig', []);
    }

    /**
     *  get method will store user data and encode a password using user entity
     * 
     * @Route("/register", methods={"post"}, name="app_register_store")
     */
    public function store(): Response
    {
        // create a new user instance then input the data then persist it to the database
        // lastly redirect the user to their personal blog route showing the create a blog button


        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        return $this->render('security/register.html.twig', []);
    }

}
