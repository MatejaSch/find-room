<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[UniqueEntity('number', message: "Room with this number already exists")]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 5)]
    #[Assert\Length(min: 1, max: 5)]
    #[Assert\Type(type: 'numeric')]
    #[Assert\PositiveOrZero]
    #[Assert\NotBlank]
    private string $number;

    #[ORM\Column(type: 'string', length: 2, nullable: true)]
    #[Assert\Length(max: 2)]
    #[Assert\Type(type: 'numeric')]
    private string $floor;

    #[ORM\Column(type: 'smallint')]
    #[Assert\Length(max: 2, groups: ['capacity'])]
    #[Assert\Type(type: 'numeric', groups: ['capacity'])]
    #[Assert\Positive(groups: ['capacity'])]
    #[Assert\NotBlank(groups: ['capacity'])]
    private int $capacity;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 300
    )]
    private string $description;

    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 0, max: 10)]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'integer')]
    private int $doubleBed;


    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 0, max: 10)]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'integer')]
    private int $singleBed;

    #[ORM\ManyToOne(targetEntity: Offer::class, inversedBy: 'rooms')]
    private $offer;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function setFloor(?string $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDoubleBed(): ?int
    {
        return $this->doubleBed;
    }

    public function setDoubleBed(int $doubleBed): self
    {
        $this->doubleBed = $doubleBed;

        return $this;
    }

    public function getSingleBed(): ?int
    {
        return $this->singleBed;
    }

    public function setSingleBed(int $singleBed): self
    {
        $this->singleBed = $singleBed;

        return $this;
    }


    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): self
    {
        $this->offer = $offer;

        return $this;
    }


}
