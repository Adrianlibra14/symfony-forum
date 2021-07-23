<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Dislike;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PostLikeController extends AbstractController
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
     * @Route("posts/{post}/likes/store", name="posts_like_store", methods={"POST"})
     */
    public function store(Post $post)
    {
        $user = $this->security->getUser();

        // Check if user already liked the post
        if($post->likedBy($user)) {
            throw $this->createNotFoundException('Not allowed');
        }
        
        $like = new Like();
        $like->setUser($user);
        $like->setPost($post);

        $entityManager = $this->getDoctrine()->getManager();

        // Remove dislike if user liked the same post
        if($post->dislikedBy($user)) {
            $dislike = $entityManager->getRepository(Dislike::class)->findOneBy([
                'user' => $user->getId(),
                'post' => $post->getId()
            ]);

            $entityManager->remove($dislike);
        }

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->redirectToRoute('posts_index');
    }

    /**
     * @Route("posts/{post}/likes/destroy", name="posts_like_destroy", methods={"POST"})
     */
    public function destroy(Post $post)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->security->getUser();

        $like = $entityManager->getRepository(Like::class)->findOneBy([
            'user' => $user->getId(),
            'post' => $post->getId()
        ]);

        if(!$like instanceof Like) {
            throw $this->createNotFoundException('Not allowed');
        }

        $entityManager->remove($like);
        $entityManager->flush();

        return $this->redirectToRoute('posts_index');
    }
}