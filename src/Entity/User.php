<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Consts\UserRolesConst;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    collectionOperations: [
        'put' => [
            "security" => "is_granted('IS_AUTHENTICATED_FULLY') and object == user",
            'normalization_context' => ['groups' => ['put_value']]
        ],
        'post' => ['normalization_context' => ['groups' => ['after_post']]],
        'get'
    ],
    itemOperations: [
        'get' => [
            "security" => "is_granted('IS_AUTHENTICATED_FULLY')",
            'normalization_context' => ['groups' => ['get_specific_user_data']]
        ],
        'put' => [
            "security" => "is_granted('IS_AUTHENTICATED_FULLY') and object == user",
            'normalization_context' => ['groups' => ['after_put']],
            'denormalization_context' => ['groups' => ['put_value']],
        ]
    ],
    normalizationContext: ['groups' => ['get_user']]
)]
#[UniqueEntity(fields: 'email', message: "Email already exist")]
#[UniqueEntity(fields: 'username', message: "Username already exist")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["get_comments_with_author", "get_specific_user_data"])]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255)]
    #[Groups(["get_comments_with_author", "get_user_of_blog_post", "get_specific_user_data"])]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["put_value"])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255)]
    #[Assert\Regex(
        '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{7,}/',
        message: "Incorrect password. The correct password should has at least 7 characters, at least one upper letter, at least one number")]
    private string $password;

    #[Assert\Expression(
        "this.getPassword() === this.getRetypedPassword()",
        message: "Given password are not the same. Please try again"
    )]
    #[Assert\NotBlank]
    #[Groups(["put_value"])]
    private string $retypedPassword;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Full name is to short should have at least 5 characters",
        maxMessage: "Full name is too long, should have at most 255 characters")]
    #[Groups(["after_put", "get_specific_comment", "get_specific_user_data"])]
    private string $fullname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Name is to short should have at least 5 characters",
        maxMessage: "Name is too long, should have at most 255 characters")]
    #[Groups(["get_user", "after_put", "after_post"])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Email is to short should have at least 5 characters",
        maxMessage: "Email is too long, should have at most 255 characters")]
    #[Assert\Email(message: "Email has incorrect format")]
    #[Groups(["get_user", "get_user_of_blog_post", "get-admin", "get-owner"])]
    private string $email;

    #[ORM\OneToMany(targetEntity: BlogPost::class, mappedBy: 'author')]
    #[Groups(["put", "get_specific_user_data"])]
    private Collection $posts;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author')]
    #[Groups(["get_user", "get_specific_user_data"])]
    private Collection $comments;

    #[ORM\Column(type: 'array')]
    #[Groups(["get-admin", "get-owner"])]
    private array $roles;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = UserRolesConst::DEFAULT_ROLES;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function setPosts(BlogPost $post): User
    {
        $this->posts->add($post);
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setComments(Comment $comment): User
    {
        $this->comments->add($comment);
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return 'ff';
    }

    public function getRetypedPassword(): string
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword(string $retypedPassword): User
    {
        $this->retypedPassword = $retypedPassword;
        return $this;
    }
}
