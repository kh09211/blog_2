<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Contact;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact/{stored}", methods={"get"}, name="contact")
     */
    public function index(ContactRepository $contactRepository, $stored = false): Response
    {
        if ($stored == 'dump') {
            // for demonstration purposes dump all the entities in the contact database

            dd($contactRepository->findAll());
        } else {

        return $this->render('contact/index.html.twig', [ 
            'mensajes' => $contactRepository->findAll(), 
            'stored' => $stored
            ]);
        }
    }


    /**
     * @Route("/contact", methods={"post"}, name="contact_store")
     */
    public function store(Request $request, ValidatorInterface $validator): RedirectResponse
    {
        // get the request form parameters from the POST
        $data = $request->request->all();

        // build the contact object
        $contact = new contact;
        $contact->setCreatedAt(new \DateTime);
        $contact->setCorreo($data['correo']);
        $contact->setNombre($data['nombre']);
        $contact->setMensaje($data['mensaje']);

        // validate the object
        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            /*
         * Uses a __toString method on the $errors variable which is a
         * ConstraintViolationList object. This gives us a nice string
         * for debugging. -- PER DOC
         */
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }
        
        //persist the message to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        // redirect to the contact page
        return $this->redirectToRoute('contact', ['stored' => true]);
    }
    
}
