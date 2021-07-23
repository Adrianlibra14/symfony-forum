<?php

namespace App\Controller;

use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserPostController extends Controller
{
    /**
     * @Route("users/{user}/posts", name="user_posts_index")
     */
    public function index(PaginatorInterface $paginator, Request $request, User $user)
    {   
        // Get the user posts
        $query = $user->getPosts();

        // Use knp_paginator to paginate the posts
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );
        
        return $this->render('user/posts/index.html.twig', ['pagination' => $pagination, 'user' => $user]);
    }
}
