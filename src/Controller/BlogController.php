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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Common\Collections\Criteria;


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
        // get all the blog objects with critera [] so all of them then order by newest first
        $blogs = $blogRepository->findBy([], ['created_at' => Criteria::DESC]);

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
        ]);
    }

    /**
     * @Route("/home", name="blog_home", methods={"GET"})
     */
    public function home(BlogRepository $blogRepository): Response
    {
        // get the authenticated user from the constructor
        $user = $this->authenticatedUser;

        // get just the users blogs and order by newest first
        // NOTE: The getBlogs() method returns a "PersistentCollection" obj so it will need some work to order properly
        
        $criteria = Criteria::create()
        ->orderBy(['created_at' => Criteria::DESC]);

        $blogs = $user->getBlogs();
        $blogs = $blogs->matching($criteria);
       
        if ($user) {
            // return only blogs for the authenticated user

            return $this->render('blog/index.html.twig', [
                'blogs' => $blogs
            ]);
        } else {
            // return to the main blog index for a list of all blogs

            return $this->redirectToRoute('blog_index');
        }
    }

    /**
     * @Route("/new", name="blog_new", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
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
        // Allow only the blog owner ability to edit - throws a 403 response
        if ($blog->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }


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
        // Allow only the blog owner ability to edit - throws a 403 response
        if ($blog->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('blog_index');
    }
}
