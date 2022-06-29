<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 80)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 5, max: 80)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: 'float')]
    #[Assert\Type(type: 'float')]
    #[Assert\NotBlank]
    private float $pricePerNight;

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Room::class)]
    private $rooms;

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: OfferImage::class, orphanRemoval: true)]
    private $offerImages;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\Column(type: 'smallint')]
    #[Assert\Length(max: 2, groups: ['capacity'])]
    #[Assert\Type(type: 'numeric', groups: ['capacity'])]
    #[Assert\Positive(groups: ['capacity'])]
    #[Assert\NotBlank(groups: ['capacity'])]
    private int $capacity;

    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 0, max: 10)]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'integer')]
    private int $singleBed;

    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 0, max: 10)]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'integer')]
    private int $doubleBed;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->offerImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPricePerNight(): ?float
    {
        return $this->pricePerNight;
    }

    public function setPricePerNight(float $pricePerNight): self
    {
        $this->pricePerNight = $pricePerNight;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms[] = $room;
            $room->setOffer($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getOffer() === $this) {
                $room->setOffer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OfferImage>
     */
    public function getOfferImages(): Collection
    {
        return $this->offerImages;
    }

    public function addOfferImage(OfferImage $offerImage): self
    {
        if (!$this->offerImages->contains($offerImage)) {
            $this->offerImages[] = $offerImage;
            $offerImage->setOffer($this);
        }

        return $this;
    }

    public function removeOfferImage(OfferImage $offerImage): self
    {
        if ($this->offerImages->removeElement($offerImage)) {
            // set the owning side to null (unless already changed)
            if ($offerImage->getOffer() === $this) {
                $offerImage->setOffer(null);
            }
        }

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

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

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

    public function getSingleBed(): ?int
    {
        return $this->singleBed;
    }

    public function setSingleBed(int $singleBed): self
    {
        $this->singleBed = $singleBed;

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
}
