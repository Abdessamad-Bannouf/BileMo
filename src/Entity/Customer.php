<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *     name="users",
 *     embedded = @Hateoas\Embedded(
 *         "expr(object.getUsers())",
 *         exclusion = @Hateoas\Exclusion(groups={"customer:list"})
 *     )
 * )
 *  
 * @Hateoas\Relation(
 *     name = "smartphones",
 *     embedded = @Hateoas\Embedded(
 *         "expr(object.getSmartphones())",
 *         exclusion = @Hateoas\Exclusion(groups={"customer:single"})
 *     )
 * )
 */
class Customer implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"customer:list", "customer:single", "user:single", "user:list", "customer:add", "customer:delete"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Serializer\Groups({"customer:list", "customer:single", "user:single", "user:list", "customer:add", "customer:delete"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups({"customer:list", "customer:single", "user:single", "user:list", "customer:add", "customer:delete"})
     * @Assert\NotBlank
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="customer", cascade={"persist"})
     * @Serializer\Expose
     * @Serializer\Groups({"customer:list"})
     * @Assert\NotBlank
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Assert\NotBlank(message="le nom d'utilisateur ne peut être nul")
     * @Assert\Length(
     *      min="5",
     *      max="20",
     *      minMessage="Votre nom d'utilisateur doit faire minimum 5 caractères",
     *      maxMessage="Votre nom d'utilisateur doit faire maximum 20 caractères"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=70)
     * @Assert\NotBlank(message="le mot de passe ne peut être nul")
     * @Assert\Length(
     *      min="8",
     *      max="20",
     *      minMessage="Votre mot de passe doit faire minimum 8 caractères",
     *      maxMessage="Votre Votre mot de passe doit faire maximum 20 caractères"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Expose
     * @Serializer\Groups({"user:single", "user:list", "customer:list", "customer:add", "customer:delete", "customer:single"})
     */
    private $roles = [];

    /**
     * @ORM\ManyToMany(targetEntity=Smartphone::class, inversedBy="customers")
     * @Serializer\Expose
     * @Serializer\Groups({"customer:single"})
     */
    private $smartphones;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->smartphones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }
    
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Smartphone>
     */
    public function getSmartphones(): Collection
    {
        return $this->smartphones;
    }

    public function addSmartphone(Smartphone $smartphone): self
    {
        if (!$this->smartphones->contains($smartphone)) {
            $this->smartphones[] = $smartphone;
        }

        return $this;
    }

    public function removeSmartphone(Smartphone $smartphone): self
    {
        $this->smartphones->removeElement($smartphone);

        return $this;
    }
}
