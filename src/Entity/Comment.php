<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => ['get_all_comments']]],
        'post' => ["security" => "is_granted('IS_AUTHENTICATED_FULLY')"],
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => ['get_specific_comment']]],
        'put' => ["security" => "is_granted('IS_AUTHENTICATED_FULLY') and object.getAuthor() == user"],
        'delete' => ["security" => "is_granted('IS_AUTHENTICATED_FULLY') and object.getAuthor() == user"],
    ],
    subresourceOperations: [
        'api_blog_posts_comments_get_subresource' => [
            'normalization_context' => ['groups' => ['get_comments_with_author']]
        ]
    ],
    normalizationContext: ['groups' => ['get']]
)]
class Comment implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["get_comments_with_author", "get_user"])]
    private ?int $id;

    #[ORM\Column(type: 'text')]
    #[Groups(["post", "get_comments_with_author", "get_specific_comment", "get_all_comments", "get_user", "blog_owner"])]
    private ?string $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["get_specific_comment", "get_all_comments"])]
    private ?\DateTimeInterface $published;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["get_comments_with_author", "get_specific_comment", "get_all_comments"])]
    private UserInterface $author;

    #[ORM\ManyToOne(targetEntity: BlogPost::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["post", "get_specific_comment", "get_all_comments"])]
    private BlogPost $blogPost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): Comment
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getAuthor(): UserInterface
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     * @return Comment
     */
    public function setAuthor(UserInterface $author): Comment
    {
        $this->author = $author;
        return $this;
    }

    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(BlogPost $blogPost): Comment
    {
        $this->blogPost = $blogPost;

        return $this;
    }

}
