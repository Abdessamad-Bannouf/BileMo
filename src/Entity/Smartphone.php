<?php

namespace App\Entity;

use App\Repository\SmartphoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SmartphoneRepository::class)
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_show_smartphone",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups={"smartphone:list"})
 * )
 */
class Smartphone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:list", "smartphone:single", "customer:single"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:list", "smartphone:single", "customer:single"})
     */
    private $designation;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:single", "customer:single"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=20)
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:single", "customer:single"})
     */
    private $color;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:list", "smartphone:single", "customer:single"})
     */
    private $price;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:single", "customer:single"})
     */
    private $year;

    /**
     * @ORM\ManyToMany(targetEntity=Customer::class, mappedBy="smartphones")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:single"})
     */
    private $customers;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->addSmartphone($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            $customer->removeSmartphone($this);
        }

        return $this;
    }
}