<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    /**
     * @Route("posts", name="posts_index")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        // Get the posts
        $query = $this->getDoctrine()->getRepository(Post::class)->findAll();
        
        // Use knp_paginator to paginate the posts
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );
        // dump($pagination);
        // exit;
        // Render posts view
        
        return $this->render('posts/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("posts/store", name="posts_store", methods={"GET", "POST"})
     */
    public function store(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();
            $user = $this->security->getUser();

            $post->setUser($user);
            $post->setCreatedAt(new \DateTime());
            $post->setUpdatedAt(new \DateTime());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("posts/{post}/update", name="posts_update", methods={"GET", "POST"})
     */
    public function update(Post $post, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $post);

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();
            $user = $this->security->getUser();

            $post->setUser($user);
            $post->setUpdatedAt(new \DateTime());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts_index');
        }
        return $this->render('posts/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("posts/{post}/destroy", name="posts_destroy", methods={"POST"})
     */
    public function destroy(Post $post)
    {
        $this->denyAccessUnlessGranted('delete', $post);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('posts_index');
    }
}
