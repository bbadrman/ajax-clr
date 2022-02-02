<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Repository\PostRepository;
use App\Repository\PostLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(PostRepository $repo)
    {
        return $this->render('post/index.html.twig', [
            'posts' => $repo->findAll(),
        ]);
    }
        /**
         * Permet de like ou unlike un article 
         * 
         * @Route("/post/{id}/like", name ="post_like")
         * @param Post
         * @return Response
         * 
         */
    public function like(Post $post, EntityManagerInterface  $manager, PostLikeRepository $likerepo)
            {

                $user = $this->getUser();
                if(!$user) return $this->json([
                    'code' => 403,
                    'message' => "Unauthorized"
                ], 403);

                if($post->isLikedByUser($user)){
                    $like = $likerepo->findOneBy([
                    'post' => $post,
                    'user' => $user
                    ]);
                    $manager->remove($like);
                    $manager->flush();

                    return $this->json([
                        'code'    => 200, 
                        'message' => 'like bien supprimer',
                        'likes'   => $likerepo->count(['post' => $post])
                    ], 200);
                }
                $like = new PostLike();
                $like->setPost($post)
                     ->setUser($user);

                $manager->persist($like);
                $manager->flush();

            return $this->json([
                'code' => 200, 
                'message' => 'Like buen ajouté',
                 'likes' => $likerepo->count(['post' => $post])         
            ], 200);
            }

}

