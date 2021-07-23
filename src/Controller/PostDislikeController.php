<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Dislike;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PostDislikeController extends Controller
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
     * @Route("posts/{post}/dislikes/store", name="posts_dislike_store", methods={"POST"})
     */
    public function store(Post $post)
    {
        $user = $this->security->getUser();

        // Check if user already disliked the post
        if($post->dislikedBy($user)) {
            throw $this->createNotFoundException('Not allowed');
        }
        
        $dislike = new Dislike();
        $dislike->setUser($user);
        $dislike->setPost($post);

        $entityManager = $this->getDoctrine()->getManager();

        // Remove like if user disliked the same post
        if($post->likedBy($user)) {
            $like = $entityManager->getRepository(Like::class)->findOneBy([
                'user' => $user->getId(),
                'post' => $post->getId()
            ]);

            $entityManager->remove($like);
        }

        $entityManager->persist($dislike);
        $entityManager->flush();

        return $this->redirectToRoute('posts_index');
    }

    /**
     * @Route("posts/{post}/dislikes/destroy", name="posts_dislike_destroy", methods={"POST"})
     */
    public function destroy(Post $post)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->security->getUser();

        $dislike = $entityManager->getRepository(Dislike::class)->findOneBy([
            'user' => $user->getId(),
            'post' => $post->getId()
        ]);

        if(!$dislike instanceof Dislike) {
            throw $this->createNotFoundException('Not allowed');
        }

        $entityManager->remove($dislike);
        $entityManager->flush();

        return $this->redirectToRoute('posts_index');
    }
}
