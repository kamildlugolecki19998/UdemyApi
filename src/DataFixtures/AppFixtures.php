<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A first post!');
        $blogPost->setPublished(new \DateTime());
        $blogPost->setContent('Post text');
        $blogPost->setAuthor('Me');
        $blogPost->setSlug('post-slug');

        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A second post!');
        $blogPost->setPublished(new \DateTime());
        $blogPost->setContent('Post text');
        $blogPost->setAuthor('Me');
        $blogPost->setSlug('post-second');

        $manager->persist($blogPost);

        $manager->flush();
    }
}
