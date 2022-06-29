<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private $createdBy;

    #[ORM\Column(type: 'date')]
    private $checkIn;

    #[ORM\Column(type: 'date')]
    private $checkOut;

    #[ORM\ManyToOne(targetEntity: Room::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private Room $room;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'boolean')]
    private $cancelled = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $cancelledBy;

    #[ORM\Column(type: 'boolean')]
    private $isReviewed = false;

    #[ORM\Column(type: 'boolean')]
    private $isCheckedIn = false;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: ReservationGuest::class, orphanRemoval: true)]
    private $reservationGuests;

    public function __construct()
    {
        $this->reservationGuests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCheckIn(): ?\DateTimeInterface
    {
        return $this->checkIn;
    }

    public function setCheckIn(\DateTimeInterface $checkIn): self
    {
        $this->checkIn = $checkIn;

        return $this;
    }

    public function getCheckOut(): ?\DateTimeInterface
    {
        return $this->checkOut;
    }

    public function setCheckOut(\DateTimeInterface $checkOut): self
    {
        $this->checkOut = $checkOut;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

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

    public function isCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): self
    {
        $this->cancelled = $cancelled;

        return $this;
    }

    public function getCancelledBy(): ?User
    {
        return $this->cancelledBy;
    }

    public function setCancelledBy(?User $cancelledBy): self
    {
        $this->cancelledBy = $cancelledBy;

        return $this;
    }

    public function isIsReviewed(): ?bool
    {
        return $this->isReviewed;
    }

    public function setIsReviewed(bool $isReviewed): self
    {
        $this->isReviewed = $isReviewed;

        return $this;
    }

    public function isIsCheckedIn(): ?bool
    {
        return $this->isCheckedIn;
    }

    public function setIsCheckedIn(bool $isCheckedIn): self
    {
        $this->isCheckedIn = $isCheckedIn;

        return $this;
    }

    /**
     * @return Collection<int, ReservationGuest>
     */
    public function getReservationGuests(): Collection
    {
        return $this->reservationGuests;
    }

    public function addReservationGuest(ReservationGuest $reservationGuest): self
    {
        if (!$this->reservationGuests->contains($reservationGuest)) {
            $this->reservationGuests[] = $reservationGuest;
            $reservationGuest->setReservation($this);
        }

        return $this;
    }

    public function removeReservationGuest(ReservationGuest $reservationGuest): self
    {
        if ($this->reservationGuests->removeElement($reservationGuest)) {
            // set the owning side to null (unless already changed)
            if ($reservationGuest->getReservation() === $this) {
                $reservationGuest->setReservation(null);
            }
        }

        return $this;
    }
}
