<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    protected \Faker\Generator $faker;

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@blog.com',
            'name' => 'Kamil Długołęcki',
            'password'=> 'Haslo1234',
            'fullName' => 'Kamil Dluglecki Admin'
        ],
        [
            'username' => 'Josh',
            'email' => 'Josh@blog.com',
            'name' => 'Josh Newt',
            'password'=> 'Haslo1234',
            'fullName' => 'Josh Newt '
        ],
        [
            'username' => 'john',
            'email' => 'john@blog.com',
            'name' => 'John Doe',
            'password'=> 'Haslo1234',
            'fullName' => 'John Doe'
        ],
    ];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create();

    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    private function loadBlogPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(50));
            $blogPost->setPublished(new \DateTime());
            $blogPost->setContent($this->faker->realText(100));
            $blogPost->setAuthor($this->getReference($this->getUserRandomUserReference()));
            $blogPost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);

            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    private function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText(30));
                $comment->setPublished(new \DateTime());

                $comment->setAuthor($this->getReference($this->getUserRandomUserReference()));
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture){
            $user = new User();

            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);
            $user->setFullname($userFixture['fullName']);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $userFixture['password']
            );
            $user->setPassword($hashedPassword);

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getUserRandomUserReference(): string
    {
        return 'user_' . self::USERS[rand(0, 2)]['username'];
    }
}
