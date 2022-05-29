<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ["security" => "is_granted('IS_AUTHENTICATED_FULLY')"],
        'post' => ["security" => "is_granted('ROLE_WRITER')"],
    ],
    itemOperations: [
        'get' => [
            "security" => "is_granted('IS_AUTHENTICATED_FULLY')",
            'normalization_context' => ['groups' => ['get_user_of_blog_post']]
        ],
        'put' => ["security" => "is_granted('ROLE_EDITOR') or (is_granted('ROLE_WRITER') and object.getAuthor() == user)"]
    ],
    denormalizationContext: ['groups' => ['post']],
)]
class BlogPost implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["get_user_of_blog_post"])]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Title must has at least 5 characters",
        maxMessage: "Title must have the most 255 characters"
    )]
    #[Groups(["post", "get_user_of_blog_post"])]
    private ?string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Content must has at least 5 characters",
        maxMessage: "Content must have the most 255 characters"
    )]
    #[Groups(["post", "get_user_of_blog_post"])]
    private ?string $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["post", "get_user_of_blog_post"])]
    private ?\DateTimeInterface $published;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Groups(["post", "get_user_of_blog_post"])]
    private ?string $slug;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["get_user_of_blog_post"])]
    private User $author;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'blogPost')]
    #[Groups(["blog_owner"])]
    #[ApiSubresource()]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setComments(ArrayCollection $comments): BlogPost
    {
        $this->comments = $comments;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): BlogPost
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): BlogPost
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setPublished(\DateTimeInterface $published): BlogPost
    {
        $this->published = $published;

        return $this;
    }

    public function setSlug(?string $slug): BlogPost
    {
        $this->slug = $slug;
        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(UserInterface $author): BlogPost
    {
        $this->author = $author;

        return $this;
    }
}
