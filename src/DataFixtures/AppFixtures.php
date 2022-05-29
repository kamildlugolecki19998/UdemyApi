<?php

namespace App\DataFixtures;

use App\Consts\UserRolesConst;
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
            'password' => 'Haslo1234',
            'fullName' => 'Kamil Dluglecki Admin',
            'roles' => [UserRolesConst::ROLE_SUPER_ADMIN]
        ],
        [
            'username' => 'Josh',
            'email' => 'Josh@blog.com',
            'name' => 'Josh Newt',
            'password' => 'Haslo1234',
            'fullName' => 'Josh Newt',
            'roles' => [UserRolesConst::ROLE_ADMIN]
        ],
        [
            'username' => 'john',
            'email' => 'john@rowlling.com',
            'name' => 'John Rowlling',
            'password' => 'Haslo1234',
            'fullName' => 'John Rowlling',
            'roles' => [UserRolesConst::ROLE_WRITER]
        ],
        [
            'username' => 'joseph',
            'email' => 'joseph@joshua.com',
            'name' => 'Joseph Joshua',
            'password' => 'Haslo1234',
            'fullName' => 'Joseph Joshua',
            'roles' => [UserRolesConst::ROLE_WRITER]
        ],
        [
            'username' => 'han_solo',
            'email' => 'hab@solo.com',
            'name' => 'Han',
            'password' => 'Haslo1234',
            'fullName' => 'Han Solo',
            'roles' => [UserRolesConst::ROLE_EDITOR]
        ],
        [
            'username' => 'jedi_knight',
            'email' => 'jedi@knight.com',
            'name' => 'Jedi',
            'password' => 'Haslo1234',
            'fullName' => 'Jedi knight',
            'roles' => [UserRolesConst::ROLE_COMMENTATOR]
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
            $blogPost->setAuthor($this->getReference($this->getUserRandomUserReference($blogPost)));
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

                $comment->setAuthor($this->getReference($this->getUserRandomUserReference($comment)));
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
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

            $user->setRoles($userFixture['roles']);

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getUserRandomUserReference($entity): string
    {
        $randomUser = self::USERS[rand(0, 5)];

        if ($entity instanceof BlogPost &&
            !count(array_intersect($randomUser['roles'],
                [UserRolesConst::ROLE_SUPER_ADMIN,
                UserRolesConst::ROLE_SUPER_ADMIN,
                UserRolesConst::ROLE_WRITER]
            ))) {
            $this->getUserRandomUserReference($entity);
        }

        if ($entity instanceof Comment &&
            !count(array_intersect($randomUser['roles'],
                [UserRolesConst::ROLE_SUPER_ADMIN,
                UserRolesConst::ROLE_SUPER_ADMIN,
                UserRolesConst::ROLE_WRITER,
                UserRolesConst::ROLE_COMMENTATOR]
            ))) {
            $this->getUserRandomUserReference($entity);
        }

        return 'user_' . self::USERS[rand(0, 5)]['username'];
    }
}
