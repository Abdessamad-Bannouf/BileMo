<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_show_user",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"user:single", "user:list"})
 * )
 *  @Hateoas\Relation(
 *     name = "shops",
 *     embedded = @Hateoas\Embedded(
 *         "expr(object.getShops())",
 *         exclusion = @Hateoas\Exclusion(groups={"user:list", "user:single", "shop:single"})
 *     )
 * )
 *     
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"user:single", "user:list", "shop:list", "shop:single", "user:delete"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Serializer\Expose
     * @Serializer\Groups({"user:single", "user:list", "shop:list", "user:delete", "shop:single"})
     * @Type("string")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Expose
     * @Serializer\Groups({"user:single", "user:list", "shop:list", "user:delete", "shop:single"})
     * @Type("array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Type("string")
     * @Serializer\Expose
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Serializer\Groups({"user:single", "user:list", "shop:list", "user:delete", "shop:single"})
     * @Type("string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Serializer\Groups({"user:single", "user:list", "shop:list", "user:delete", "shop:single"})
     * @Type("string")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Serializer\Groups({"user:single", "user:list", "shop:list", "user:delete", "shop:single"})
     * @Type("string")
     */
    private $username;

    /**
     * @ORM\ManyToMany(targetEntity=Shop::class, mappedBy="users", orphanRemoval=true, cascade={"persist"})
     * @Serializer\Groups({"user:single", "user:delete"})
     * @Serializer\Expose
     * @Type("ArrayCollection")
     */
    public $shops;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->username;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addShop(Shop $shop): self
    {
        
        if (!$this->shops->contains($shop)) {
            $this->shops[] = $shop;
            $shop->addUser($this);
        }

        return $this;
    }

    public function removeShop(Shop $shop): self
    {
        if ($this->shops->removeElement($shop)) {
            $shop->removeUser($this);
        }

        return $this;
    }
}
