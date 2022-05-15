<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class BlogPostController extends AbstractController
{
    private const POST = [

        [
            'id' => 1,
            'slug' => 'hello-world',
            'name' => 'Hello world',
        ],
        [
            'id' => 2,
            'slug' => 'second-example',
            'name' => 'Second example',
        ],
        [
            'id' => 3,
            'slug' => 'last-example',
            'name' => 'Last Example',
        ]
    ];


    #[Route(
        '/blog/post',
        name: 'app_blog_post'
    )]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BlogPostController.php',
        ]);
    }

    /**
     * @param BlogPostRepository $blogPostRepository
     * @return Response
     */
    #[Route(
        '/blog/post/list',
        name: 'blog_list',
        methods: ['GET'],
    )]
    public function list(BlogPostRepository $blogPostRepository): Response
    {

        $blogPosts = $blogPostRepository->findAll();

//        return $blogPosts;
        return $this->json($blogPosts);
    }

    #[Route(
        '/blog/post/{id}',
        name: 'post_by_id',
        requirements: ['id' => '\d+'],
        methods: ['GET'],

    )]
    public function postById(BlogPost $post): Response
    {
//
//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/BlogPostController.php',
//        ]);

//        $blog = $blogPostRepository->find($id);

        return $this->json($post);
    }

    #[Route(
        '/blog/post/{slug}',
        name: 'post_by_slug',
        methods: ['GET'],
    )]
    #[ParamConverter('post', class: BlogPost::class, options: ['mapping' => ['slug' => 'slug']],)]
    public function postBySlug(BlogPost $post): Response
    {
        return $this->json($post);
    }


    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param SerializerInterface $serializer
     * @return Response
     */
    #[Route(
        '/blog/post/add',
        name: 'add_blog',
        methods: ['POST']
    )]
    public function add(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $entityManager = $doctrine->getManager();
        $entityManager->persist($blogPost);
        $entityManager->flush();

        return $this->json($blogPost);
    }

    #[Route('blog/post/{id}', name: 'delete_by_id', methods: ['DELETE'])]
    #[ParamConverter('blogPost', class: BlogPost::class)]
    public function deleteBlog(ManagerRegistry $managerRegistry, BlogPost $blogPost): Response
    {
        $em = $managerRegistry->getManager();
        $em->remove($blogPost);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
