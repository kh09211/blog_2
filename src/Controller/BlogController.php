<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security; // used for fetching the current user object
use Symfony\Component\HttpFoundation\RedirectResponse; // use for redirects


/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    public function __construct(Security $security) {
        // used for getting the user object
        $this->security = $security;

        // since we will be using the authenticated user multiple times in this controller, define it here
        $this->authenticatedUser = $security->getUser();
    
    }

    /**
     * @Route("/", name="blog_index", methods={"GET"})
     */
    public function index(BlogRepository $blogRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/home", name="blog_home", methods={"GET"})
     */
    public function home(BlogRepository $blogRepository): Response
    {
        // get the authenticated user from the constructor
        $user = $this->authenticatedUser;
       
        if ($user) {
            // return only blogs for the authenticated user

            return $this->render('blog/index.html.twig', [
                'blogs' => $user->getBlogs()
            ]);
        } else {
            // return to the main blog index for a list of all blogs

            return $this->redirectToRoute('blog_index');
        }
    }

    /**
     * @Route("/new", name="blog_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $blog = new Blog();
        $blog->setUser($this->authenticatedUser); // add the user to the object
        $blog->setCreatedAt(new \DateTime()); // timestamp it for NOW
       
        
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blog_show", methods={"GET"})
     */
    public function show(Blog $blog): Response
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="blog_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Blog $blog): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blog_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Blog $blog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('blog_index');
    }
}
