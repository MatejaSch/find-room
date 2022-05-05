<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 5)]
    #[Assert\Length(min: 1, max: 5)]
    #[Assert\Type(type: 'numeric')]
    #[Assert\PositiveOrZero]
    #[Assert\NotBlank]
    private $number;

    #[ORM\Column(type: 'string', length: 2, nullable: true)]
    #[Assert\Length(max: 2)]
    #[Assert\Type(type: 'numeric')]
    private $floor;

    #[ORM\Column(type: 'smallint')]
    #[Assert\Length(max: 2)]
    #[Assert\Type(type: 'numeric')]
    #[Assert\Positive]
    #[Assert\NotBlank]
    private $capacity;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 300
    )]
    private $description;

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
}
