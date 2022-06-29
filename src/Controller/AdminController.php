<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\OfferImage;
use App\Entity\Reservation;
use App\Entity\ReservationGuest;
use App\Entity\Room;
use App\Entity\User;
use App\Form\OfferType;
use App\Form\ReservationGuestType;
use App\Form\RoomType;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{

    private ManagerRegistry $doctrine;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine)
    {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;

    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/room/add', name: 'admin_room_add')]
    public function addRoom(Request $request, ImageUploader $imageUploader): Response
    {
        $form = $this->createForm(RoomType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Room $room */
            $room = $form->getData();
            $room->setCapacity($room->getDoubleBed()*2 + $room->getSingleBed());


            //Check room - offer compatibility is offer is set
            /** @var Offer $offer */
            $offer = $room->getOffer();
            if ($offer !== null) {
                if ($room->getCapacity() !== $offer->getCapacity() || $room->getSingleBed() !== $offer->getSingleBed()
                    || $room->getSingleBed() !== $offer->getDoubleBed()) {

                    $offerError = "Offer isn't compatible with room!";
                    return $this->renderForm('admin/room_add.html.twig', [
                        'form' => $form,
                        'offerError' => $offerError,
                        'buttonAddRoomActive' => 1
                    ]);
                }
            }


            $this->entityManager->persist($room);
            $this->entityManager->flush();

            $this->addFlash("success", "Successfully added new room!" );
            return $this->redirect($request->getUri());
        }

        return $this->renderForm('admin/room_add.html.twig', [
            'form' => $form,
            'buttonAddRoomActive' => 1
        ]);
    }


    #[Route('admin/rooms', name: 'admin_rooms')]
    public function showRooms(): Response
    {
        /** @var Room  $rooms */
        $rooms = $this->doctrine->getRepository(Room::class)->findBy(array(), ['offer' => 'ASC', 'number' => 'ASC']);

        return $this->render("admin/rooms.html.twig", [
            'rooms' => $rooms,
            'buttonShowAllRoomsActive' => true
        ]);
    }


    #[Route('admin/room/delete/{id}', name: 'admin_room_delete')]
    public function deleteRoom(int $id): Response
    {
        $room = $this->doctrine->getRepository(Room::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($room);
        $this->entityManager->flush();
        $this->addFlash("success", "Successfully removed room!");
        return $this->redirectToRoute('admin_rooms');
    }


    #[Route('admin/room/{id}', name: 'admin_room')]
    public function editRoom(int $id, Request $request, ImageUploader $imageUploader): Response
    {
        /** @var Room $room */
        $room = $this->doctrine->getRepository(Room::class)->findOneBy(['id' => $id]);

        //Redirect if room with ID provided by GET parameter doesn't exist
        if (!$room) {
            $this->redirectToRoute("admin_rooms");
        }

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Room $room */
            $room = $form->getData();
            $room->setCapacity($room->getDoubleBed()*2 + $room->getSingleBed());

            //Check room - offer compatibility is offer is set
            /** @var Offer $offer */
            $offer = $room->getOffer();

            if ($offer !== null) {
                if ($room->getCapacity() !== $offer->getCapacity() || $room->getSingleBed() !== $offer->getSingleBed()
                    || $room->getDoubleBed() !== $offer->getDoubleBed()) {

                    $offerError = "Offer isn't compatible with room!";
                    return $this->renderForm('admin/room_add.html.twig', [
                        'form' => $form,
                        'offerError' => $offerError,
                        'room' => $room
                    ]);
                }
            }

            $this->entityManager->persist($room);
            $this->entityManager->flush();

            $this->addFlash("success", "Successfully updated room info!" );
            return $this->redirect($request->getUri());
        }

        return $this->renderForm("admin/room.html.twig", [
            'form' => $form,
            'room' => $room,
        ]);

    }

    #[Route('admin/offer/add', name: 'admin_offer_add')]
    public function addOffer(Request $request, ImageUploader $imageUploader) : Response
    {
        $form = $this->createForm(OfferType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Offer $offer */
            $offer = $form->getData();
            $this->entityManager->persist($offer);
            $offer->setCapacity($offer->getDoubleBed()*2 + $offer->getSingleBed());

            //Uploads images and registers them in database
            /** @var UploadedFile[] $images */
            $images = $form->get('images')->getData();
            if (count($images) !== 0) {
                foreach ($images as $imageFile) {
                    try {
                        $newFilename = $imageUploader->uploadImage($imageFile);

                        /** @var OfferImage offerImage */
                        $offerImage = new OfferImage();
                        $offerImage->setImageName($newFilename)->setOffer($offer);
                        $this->entityManager->persist($offerImage);
                    } catch (FileException $e) {
                        //Redirect with error flash message
                    }
                }
            }

            $this->entityManager->flush();
            $this->addFlash("success", "Successfully added new offer! Please set offer thumbnail image." );
            return $this->redirectToRoute('admin_offer', [
                'id' => $offer->getId()
            ]);
        }

        return $this->renderForm("admin/offer_add.html.twig", [
            'form' => $form,
            'buttonAddOfferActive' => 1
        ]);
    }

    #[Route('admin/offers', name: 'admin_offers')]
    public function showOffers() : Response
    {
        /** @var Offer[] $offers */
        $offers = $this->doctrine->getRepository(Offer::class)->findBy(array(), ['thumbnail' => 'ASC']);

        return $this->render("admin/offers.html.twig", [
            'offers' => $offers,
            'buttonShowAllOffersActive' => 1
        ]);
    }

    #[Route('admin/offer/delete/{id}', name: 'admin_offer_delete')]
    public function deleteOffer(int $id, Request $request) : Response
    {
        /** @var Offer $offer */
        $offer = $this->doctrine->getRepository(Offer::class)->findOneBy(['id' => $id]);

        /** @var Room[] $rooms */
        $rooms = $offer->getRooms();
        foreach ($rooms as $room) {
            $room->setOffer(null);
            $this->entityManager->persist($room);
        }
        $this->entityManager->flush();

        $this->entityManager->remove($offer);
        $this->entityManager->flush();

        $this->addFlash("success", "Successfully removed offer!");
        return $this->redirectToRoute('admin_offers');
    }


    #[Route('admin/offer/{id}', name: 'admin_offer')]
    public function editOffer(int $id, Request $request, ImageUploader $imageUploader) : Response
    {
        /** @var Offer $offer */
        $offer = $this->doctrine->getRepository(Offer::class)->findOneBy(['id' => $id]);

        /** @var Offer $oldOffer */
        $oldOffer = clone $offer;

        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        /** @var OfferImage[] $offerImages */
        $offerImages = $offer->getOfferImages();

        if ($form->isSubmitted() && $form->isValid()) {

            //If capacity of offer is changed, unlink all rooms from offer
            if ($oldOffer->getSingleBed() !== $offer->getSingleBed() || $oldOffer->getDoubleBed() !== $offer->getDoubleBed()) {
                /** @var Room[] $connectedRooms */
                $connectedRooms = $offer->getRooms();
                foreach ($connectedRooms as $room) {
                    $room->setOffer(null);
                    $this->entityManager->persist($room);
                    $this->entityManager->flush();
                }
            }

            $offer->setCapacity($offer->getDoubleBed()*2 + $offer->getSingleBed());

            //Set thumbnail if image for thumbnail selected
            if ($request->request->get('thumbnail')) {
                $offer->setThumbnail($request->request->get('thumbnail'));
            }

            //Uploads images and registers them in database
            /** @var UploadedFile[] $images */
            $images = $form->get('images')->getData();
            if (count($images) !== 0) {
                foreach ($images as $imageFile) {
                    try {
                        $newFilename = $imageUploader->uploadImage($imageFile);

                        /** @var OfferImage offerImage */
                        $offerImage = new OfferImage();
                        $offerImage->setImageName($newFilename)->setOffer($offer);
                        $this->entityManager->persist($offerImage);
                    } catch (FileException $e) {
                        //Redirect with error flash message
                    }
                }
            }

            //Removing images from room that user selected
            $imagesToRemove = $request->request->get("remove-image-ids");
            $imagesToRemove = $imagesToRemove !== "" ? explode( ";", trim($imagesToRemove, ";")) : [];
            foreach ($imagesToRemove as $imageID) {
                /** @var OfferImage $removeImage */
                $removeImage = $this->doctrine->getRepository(OfferImage::class)->findOneBy(["id" => $imageID, "offer" => $offer]);
                if (null !== $removeImage){
                    if ($offer->getThumbnail() === $removeImage->getImageName()) {
                        $offer->setThumbnail(null);
                    }
                    $this->entityManager->remove($removeImage);
                }
            }

        $this->entityManager->flush();

        $this->addFlash("success", "Successfully updated offer info!" );
        return $this->redirect($request->getUri());
        }

        return $this->renderForm("admin/offer.html.twig", [
            'offer' => $offer,
            'form' => $form,
            'images' => $offerImages
        ]);
    }

    #[Route('admin/users', name: 'admin_users')]
    public function showUsers(Request $request) : Response
    {
        /** @var User[] $users */
        $users = $this->doctrine->getRepository(User::class)->findAll();

        return $this->render("admin/users.html.twig", [
            'users' => $users,
            'buttonShowUsersActive' => 1
        ]);
    }

    #[Route('admin/user/deny-access/{id}', name: 'admin_user_deny_access')]
    public function denyUserAccess(int $id, Request $request) : Response
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findNonAdminUserByID($id);

        if($user === null || $user->isIsBanned() === true) {
            return $this->redirectToRoute("admin_users");
        }

        $user->setIsBanned(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash("success", "User access denied!");
        return $this->redirectToRoute("admin_users");
    }

    #[Route('admin/user/allow-access/{id}', name: 'admin_user_allow_access')]
    public function allowUserAccess(int $id, Request $request) : Response
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findNonAdminUserByID($id);


        if($user === null || $user->isIsBanned() === false) {
            return $this->redirectToRoute("admin_users");
        }

        $user->setIsBanned(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash("success", "User access allowed!");
        return $this->redirectToRoute("admin_users");
    }

    #[Route('admin/user/{id}/reservations', name: 'admin_user_reservations')]
    public function viewUserReservations(int $id, Request $request) : Response
    {

        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['id' => $id]);

        /** @var Reservation[] $reservations */
        $reservations = $this->doctrine->getRepository(Reservation::class)->findBy(['createdBy' => $user, 'cancelled' => false, 'isCheckedIn' => false]);

        /** @var Reservation[] $cancelledReservations */
        $cancelledReservations = $this->doctrine->getRepository(Reservation::class)->findBy(['createdBy' => $user, 'cancelled' => true]);

        /** @var Reservation[] $finishedReservations */
        $olderReservations = $this->doctrine->getRepository(Reservation::class)->findBy(['createdBy' => $user, 'isCheckedIn' => true]);

        return $this->render('admin/user_reservations.html.twig', [
            'reservations' => $reservations,
            'cancelledReservations' => $cancelledReservations,
            'olderReservations' => $olderReservations,
            'user' => $user
        ]);
    }

    #[Route('admin/reservation/cancel/{reservationID}', name: 'admin_user_reservation_cancel')]
    public function cancelReservation(int $reservationID, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Reservation $reservation */
        $reservation = $this->doctrine->getRepository(Reservation::class)->findOneBy(['id' => $reservationID, 'cancelled' => false, 'checkIn' => false]);
        if ($reservation === null) {
            return $this->redirectToRoute("admin");
        }
        $reservation->setCancelled(true);
        $reservation->setIsReviewed(true);
        $reservation->setCancelledBy($user);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();


        $this->addFlash("success", "Reservation has been cancelled");
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('admin/reservation/accept/{reservationID}', name: 'admin_user_reservation_accept')]
    public function acceptReservation(int $reservationID, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Reservation $reservation */
        $reservation = $this->doctrine->getRepository(Reservation::class)->findOneBy(['id' => $reservationID, 'cancelled' => false, 'isReviewed' => false, 'isCheckedIn' => false]);
        if ($reservation === null) {
            return $this->redirectToRoute("admin");
        }
        $reservation->setCancelled(false);
        $reservation->setIsReviewed(true);
        $reservation->setCancelledBy($user);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();


        $this->addFlash("success", "Reservation has been accepted");
        return $this->redirect($request->headers->get('referer'));
    }


    #[Route('admin/reservations/new', name: 'admin_reservations_new')]
    public function showNewReservations(): Response
    {
        /** @var Reservation[] $newReservations */
        $newReservations = $this->doctrine->getRepository(Reservation::class)->findBy(['cancelled' => false, 'isReviewed' => false, 'isCheckedIn' => false]);


        return $this->render("admin/reservations_new.html.twig", [
            'newReservations' => $newReservations,
            'buttonShowNewReservationsActive' => 1
        ]);


    }

    #[Route('admin/reservations/active', name: 'admin_reservations_active')]
    public function showReviewedReservations(): Response
    {

        /** @var Reservation[] $activeReservations */
        $activeReservations = $this->doctrine->getRepository(Reservation::class)->findBy(['cancelled' => false, 'isReviewed' => true, 'isCheckedIn' => false]);

        return $this->render('admin/reservations_active.html.twig', [
            'activeReservations' => $activeReservations,
            'buttonShowReviewedReservationsActive' => 1
        ]);


    }

    #[Route('admin/reservations/cancelled', name: 'admin_reservations_cancelled')]
    public function showCancelledReservations(): Response
    {

        /** @var Reservation[] $cancelledReservations */
        $cancelledReservations = $this->doctrine->getRepository(Reservation::class)->findBy(['cancelled' => true, 'isReviewed' => true, 'isCheckedIn' => false]);


        return $this->render('admin/reservations_cancelled.html.twig', [
            'cancelledReservations' => $cancelledReservations,
            'buttonShowCancelledReservationsActive' => 1
        ]);


    }

    #[Route('admin/reservations/checked-in', name: 'admin_reservations_checked_in')]
    public function showCheckedInReservations(): Response
    {
        /** @var Reservation[] $checkedInReservations */
        $checkedInReservations = $this->doctrine->getRepository(Reservation::class)->findBy(['isCheckedIn' => true, 'isReviewed' => true, 'cancelled' => false]);

        return $this->render('admin/reservations_checked.html.twig', [
            'checkedInReservations' => $checkedInReservations,
            'buttonShowCheckedInReservations' => 1
        ]);

    }

    #[Route('admin/reservation/check-in/{id}/add-guests', name: 'admin_reservations_check_in_add_guests')]
    public function checkInReservationAddGuests(int $id, Request $request): Response
    {
        /** @var Reservation $reservation */
        $reservation = $this->doctrine->getRepository(Reservation::class)->findOneBy(['id' => $id, 'isCheckedIn' => false, 'isReviewed' => true, 'cancelled' => false]);

        if($reservation === null) {
            $this->redirectToRoute("admin");
        }

        /** @var ReservationGuest[]  $reservationGuests*/
        $reservationGuests = $this->doctrine->getRepository(ReservationGuest::class)->findBy(['reservation' => $reservation->getId()]);

        $guestForm = $this->createForm(ReservationGuestType::class, null, [
            'reservationID' => $reservation->getId(),
        ]);
        $guestForm->handleRequest($request);

        if ($guestForm->isSubmitted() && $guestForm->isValid()) {
            /** @var ReservationGuest $newGuest */
            $newGuest = $guestForm->getData();
            $newGuest->setReservation($reservation);
            $this->entityManager->persist($newGuest);
            $this->entityManager->flush();

            return $this->redirect($request->getUri());
        }

        return $this->renderForm('admin/reservation_check_in.html.twig', [
            'guestForm' => $guestForm,
            'guests' => $reservationGuests,
            "reservationID" => $reservation->getId()
        ]);

    }

    #[Route('admin/reservation/check-in/{reservationID}/remove-guest/{guestID}', name: 'admin_reservations_check_in_remove_guest')]
    public function checkInReservationRemoveGuest(int $reservationID, int $guestID, Request $request): Response
    {
        /** @var Reservation $reservation */
        $reservation = $this->doctrine->getRepository(Reservation::class)->findOneBy(['id' => $reservationID, 'isCheckedIn' => false, 'isReviewed' => true, 'cancelled' => false]);

        if($reservation === null) {
            $this->redirectToRoute("admin");
        }

        /** @var ReservationGuest  $reservationGuest */
        $reservationGuest = $this->doctrine->getRepository(ReservationGuest::class)->findOneBy(['reservation' => $reservation->getId(), 'id' => $guestID]);

        if($reservationGuest === null) {
            $this->redirectToRoute("admin");
        }

        $this->entityManager->remove($reservationGuest);
        $this->entityManager->flush();

        return $this->redirectToRoute("admin_reservations_check_in_add_guests", [
            'id' => $reservationID
        ]);

    }

    #[Route('admin/reservation/check-in/{reservationID}', name: 'admin_reservations_check_in')]
    public function checkInReservation(int $reservationID, Request $request): Response
    {
        /** @var Reservation $reservation */
        $reservation = $this->doctrine->getRepository(Reservation::class)->findOneBy(['id' => $reservationID, 'isCheckedIn' => false, 'isReviewed' => true, 'cancelled' => false]);

        if($reservation === null) {
            $this->redirectToRoute("admin");
        }


        if(count($reservation->getReservationGuests()) > 0) {
            $reservation->setIsCheckedIn(true);
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        }

        $this->addFlash("success", "Reservation checked in!");
        return $this->redirectToRoute("admin_reservations_checked_in");

    }

    #[Route('admin/reservation/checked-in/{reservationID}/guests', name: 'admin_reservations_check_in_guests')]
    public function checkInReservationGuests(int $reservationID, Request $request): Response
    {
        /** @var Reservation $reservation */
        $reservation = $this->doctrine->getRepository(Reservation::class)->findOneBy(['id' => $reservationID, 'isCheckedIn' => true, 'isReviewed' => true, 'cancelled' => false]);


        if($reservation === null) {
            $this->redirectToRoute("admin");
        }

        return $this->render("reservation_check_in_guests.html.twig", [
            'reservation' => $reservation,
        ]);

    }




}
