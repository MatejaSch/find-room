<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 80)]
    private string $title;

    #[ORM\Column(type: 'float')]
    private float $pricePerNight;

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Room::class)]
    private $rooms;

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: OfferImage::class, orphanRemoval: true)]
    private $offerImages;

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
}
