<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Reservation;
use App\Entity\User;
use App\Form\ReservationType;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $entityManager)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
    }

    #[Route('/reservations', name: 'app_reservations')]
    public function reservations(Request $request): Response
    {
        $user = $this->getUser();

        if($user === null) {
            return $this->redirectToRoute("app_home");
        }

        /** @var Reservation[] $reservations */
        $reservations = $this->doctrine->getRepository(Reservation::class)->findBy(['createdBy' => $user, 'cancelled' => false, 'isCheckedIn' => false]);


        //TODO show only recently canceled in last 30 days...
        /** @var Reservation[] $cancelledReservations */
        $cancelledReservations = $this->doctrine->getRepository(Reservation::class)->findBy(['createdBy' => $user, 'cancelled' => true]);


        return $this->render('reservation/reservations.html.twig', [
            'reservations' => $reservations,
            'cancelledReservations' => $cancelledReservations,
        ]);
    }

    #[Route('/reservation/cancel/{id}', name: 'app_cancel_reservation')]
    public function cancelReservation(int $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if($user === null) {
            return $this->redirectToRoute("app_home");
        }

        /** @var Reservation $reservation */
        $reservation = $this->doctrine->getRepository(Reservation::class)->findOneBy(['id' => $id, 'createdBy' => $user, 'cancelled' => false]);
        if ($reservation === null) {
            return $this->redirectToRoute("app_home");
        }
        $reservation->setCancelled(true);
        $reservation->setIsReviewed(true);
        $reservation->setCancelledBy($user);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();


        $this->addFlash("success", "Reservation has been cancelled");
        return $this->redirectToRoute("app_reservations");
    }



}
