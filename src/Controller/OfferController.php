<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use App\Form\FilterOffersType;
use App\Form\ReservationType;
use App\Form\RoomAvailabilityType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $doctrine;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine)
    {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
    }

    #[Route('/offers', name: 'app_offers', methods: ["GET"])]
    public function showOffers(ManagerRegistry $doctrine, Request $request): Response
    {
        /** @var Offer[] $offers */
        $offers = $this->doctrine->getRepository(Offer::class)->findAvailableOffers();

        $capacity = null;

        $formFilter = $this->createForm(FilterOffersType::class, null, [
            'method' => 'GET',
        ]);
        $formFilter->handleRequest($request);

        if ($formFilter->isSubmitted() && $formFilter->isValid()) {
            if ($formFilter->get('capacity')->getData() !== null) {
                $capacity = $formFilter->get('capacity')->getData();
            }

            //Get all offers with certain capacity
            /** @var Offer[] $offers */
            $offers = $this->doctrine->getRepository(Offer::class)->findAvailableOffers($capacity);

            if ($formFilter->get('checkIn')->getData() !== null && $formFilter->get('checkOut')->getData() !== null) {
                $checkIn = $formFilter->get('checkIn')->getData();
                $checkOut = $formFilter->get('checkOut')->getData();

                if ($checkIn >= $checkOut) {
                    $checkIn = null;
                    $checkOut = null;
                }

                //Filter offers - check if offer has available rooms for chosen dates
                /** @var Offer[] $offersWithAvailableRooms */
                $offersWithAvailableRooms = [];
                foreach ($offers as $offer) {
                    /** @var Room[] $rooms */
                    $rooms = $offer->getRooms();
                    foreach ($rooms as $room) {
                        $reservationFound = $this->doctrine->getRepository(Reservation::class)->isRoomReservedInPeriod($room, $checkIn, $checkOut);
                        //Save offer when come across first available room
                        if (count($reservationFound) === 0) {
                            $offersWithAvailableRooms[] = $offer;
                            break;
                        }
                    }
                }

                $offers = $offersWithAvailableRooms;
            }

            return $this->renderForm("offer/offers.html.twig", [
                'offers' => $offers,
                'formFilter' => $formFilter,
            ]);

        }

        return $this->renderForm("offer/offers.html.twig", [
            'offers' => $offers,
            'formFilter' => $formFilter
        ]);
    }

    #[Route('/offer/{id}', name: 'app_offer', methods: ['GET'])]
    public function showOffer(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        /** @var Offer $offer */
        $offer = $this->doctrine->getRepository(Offer::class)->findOfferByID($id);

        if (null === $offer) {
            return $this->redirectToRoute('app_offers');
        }

        //Availability form
        $formAvailability = $this->createForm(RoomAvailabilityType::class, null, [
            'action' => $this->generateUrl('app_offer_room_availability'),
            'offerID' => $offer->getId(),
            'method' => "POST",
        ]);

        $userEmail = null;
        if ($this->getUser()) {
            $userEmail = $this->getUser()->getEmail();
        }

        //Reservation form
        $formReservation = $this->createForm(ReservationType::class, null, [
            'user' => $userEmail,
            'offer' => $offer->getTitle(),
            'price' => $offer->getPricePerNight(),
            'offerID' => $offer->getId()
        ]);


        return $this->renderForm("offer/offer.html.twig", [
            'offer' => $offer,
            'formAvailability' => $formAvailability,
            'formReservation' => $formReservation,
        ]);
    }

    #[Route('offer/room-availability', name: "app_offer_room_availability", methods: ["POST"])]
    public function checkRoomAvailability(Request $request)
    {
        $form = $this->createForm(RoomAvailabilityType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $checkIn = $form->get("checkIn")->getData();
            $checkOut = $form->get("checkOut")->getData();
            $offerID = $form->get("offerID")->getData();
            $offer = $this->doctrine->getRepository(Offer::class)->findOneBy(['id' => $offerID]);

            if ($checkIn >= $checkOut) {
                return $this->json(['error' => 'Error has occurred']);
            }

            /** @var Room[] $rooms */
            $rooms = $offer->getRooms();
            foreach ($rooms as $room) {
                $reservationFound = $this->doctrine->getRepository(Reservation::class)->isRoomReservedInPeriod($room, $checkIn, $checkOut);

                if (count($reservationFound) === 0) {
                    $message = "There is available room " . date_format($checkIn, "m-d-Y") . " to " . date_format($checkOut, "m-d-Y");
                    return $this->json(['success' => $message]);
                }
            }
            $message = "There is no available room " . date_format($checkIn, "m-d-Y") . " to " . date_format($checkOut, "m-d-Y");
            return $this->json(['error' => $message]);

        }
        return $this->json(['error' => 'Error has occurred']);

    }

    #[Route('offer/book-room', name: "app_offer_book_room", methods: ["POST"])]
    public function bookRoom(Request $request)
    {
        $form = $this->createForm(ReservationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();

            $checkIn = $form->get("checkIn")->getData();
            $checkOut = $form->get("checkOut")->getData();
            if ($checkIn >= $checkOut) {
                return $this->json(['error' => 'Error has occurred']);
            }

            /** @var Offer $offer */
            $offerID = $form->get("offerID")->getData();
            $offer = $this->doctrine->getRepository(Offer::class)->findOfferByID($offerID);
            if (null === $offer) {
                return $this->json(['error' => 'Error has occurred']);
            }

            $nights = date_diff($checkIn, $checkOut)->d;
            $price = $nights * $offer->getPricePerNight();

            /** @var Room[] $rooms */
            $rooms = $offer->getRooms();

            foreach ($rooms as $room) {
                $reservationFound = $this->doctrine->getRepository(Reservation::class)->isRoomReservedInPeriod($room, $checkIn, $checkOut);

                if (count($reservationFound) === 0) {
                    $reservation = new Reservation();
                    $reservation->setCreatedBy($user)->setRoom($room)->setCheckIn($checkIn)->setCheckOut($checkOut)->setPrice($price);
                    $this->entityManager->persist($reservation);
                    $this->entityManager->flush();
                    $message = "Room has been booked on " . date_format($checkIn, "m-d-Y") . " to " . date_format($checkOut, "m-d-Y");
                    return $this->json(['success' => $message]);
                }

            }
            $message = "There is no available room " . date_format($checkIn, "m-d-Y") . " to " . date_format($checkOut, "m-d-Y");
            return $this->json(['error' => $message]);

        }
        return $this->json(['error' => 'Error has occurred']);

    }


}

