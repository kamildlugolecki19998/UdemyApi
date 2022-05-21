<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    itemOperations: ['get'],
    collectionOperations: ['any'],
    normalizationContext: ['groups' => ['read']]
)]
#[UniqueEntity(fields: 'email', message: "Email already exist")]
#[UniqueEntity(fields: 'username', message: "Username already exist")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255)]
    private $username;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255)]
    #[Assert\Regex(
        '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{7,}/',
        message: "Incorrect password. The correct password should has at least 7 characters, at least one upper letter, at least one number")]
    private $password;

    #[Assert\Expression(
        "this.getPassword() === this.getRetypedPassword()",
        message: "Given password are not the same. Please try again"
    )]
    #[Assert\NotBlank]
    private $retypedPassword;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Full name is to short should have at least 5 characters",
        maxMessage: "Full name is too long, should have at most 255 characters")]
    private $fullname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Name is to short should have at least 5 characters",
        maxMessage: "Name is too long, should have at most 255 characters")]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Email is to short should have at least 5 characters",
        maxMessage: "Email is too long, should have at most 255 characters")]
    #[Assert\Email(message: "Email has incorrect format")]
    private $email;

    #[ORM\OneToMany(targetEntity: BlogPost::class, mappedBy: 'author')]
    #[Groups(['read'])]
    private $posts;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author')]
    #[Groups(['read'])]
    private $comments;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    /**
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @param BlogPost $posts
     * @return User
     */
    public function setPosts(BlogPost $post): User
    {
        $this->posts->add($post);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     * @return User
     */
    public function setComments(Comment $comment): User
    {
        $this->comments->add($comment);
        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLES'];
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
