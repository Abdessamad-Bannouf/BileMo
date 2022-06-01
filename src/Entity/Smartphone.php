<?php

namespace App\Entity;

use App\Repository\SmartphoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=SmartphoneRepository::class)
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_show_smartphone",
 *          parameters = {
 *              "id" = "expr(object.getId())"
 *          }
 *      )
 * )
 */
class Smartphone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:list", "smartphone:single"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:list", "smartphone:single"})
     */
    private $designation;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:single"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=20)
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:single"})
     */
    private $color;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:list", "smartphone:single"})
     */
    private $price;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Expose
     * @Serializer\Groups({"smartphone:single"})
     */
    private $year;

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
}