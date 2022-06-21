<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Room;
use App\Entity\RoomImage;
use App\Entity\User;
use App\Form\OfferType;
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
            $this->entityManager->persist($room);

//            //Uploads images and registers them in database
//            /** @var UploadedFile[] $images */
//            $images = $form->get('images')->getData();
//            if (count($images) !== 0) {
//                foreach ($images as $imageFile) {
//                    try {
//                        $newFilename = $imageUploader->uploadImage($imageFile);
//
//                        /** @var RoomImage roomImage */
//                        $roomImage = new RoomImage();
//                        $roomImage->setFileName($newFilename)->setRoom($room);
//                        $this->entityManager->persist($roomImage);
//                    } catch (FileException $e) {
//                        //Redirect with error flash message
//                    }
//                }
//            }

            $this->entityManager->flush();

            $this->addFlash("success", "Successfully added new room!" );
            return $this->redirect($request->getUri());
        }

        return $this->renderForm('admin/room_add.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('admin/rooms/', name: 'admin_rooms')]
    public function showRooms(): Response
    {
        /** @var Room  $rooms */
        $rooms = $this->doctrine->getRepository(Room::class)->findAll();

        return $this->render("admin/rooms.html.twig", [
            'rooms' => $rooms,
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

//        /** @var RoomImage[] $roomImages */
//        $roomImages = $this->doctrine->getRepository(RoomImage::class)->findBy(['room' => $id]);

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
            $this->entityManager->persist($room);

//            //Uploads images and registers them in database
//            /** @var UploadedFile[] $images */
//            $images = $form->get('images')->getData();
//            if (count($images) !== 0) {
//                foreach ($images as $imageFile) {
//                    try {
//                        $newFilename = $imageUploader->uploadImage($imageFile);
//
//                        /** @var RoomImage roomImage */
//                        $roomImage = new RoomImage();
//                        $roomImage->setFileName($newFilename)->setRoom($room);
//                        $this->entityManager->persist($roomImage);
//                    } catch (FileException $e) {
//                        //Redirect with error flash message
//                    }
//                }
//            }
//
//            //Removing images from room that user selected
//            $imagesToRemove = $request->request->get("remove-image-ids");
//            $imagesToRemove = $imagesToRemove !== "" ? explode( ";", trim($imagesToRemove, ";")) : [];
//            foreach ($imagesToRemove as $imageID) {
//                /** @var RoomImage  $removeImage */
//                $removeImage = $this->doctrine->getRepository(RoomImage::class)->findOneBy(["id" => $imageID, "room" => $room]);
//                if (null !== $removeImage){
//                    $this->entityManager->remove($removeImage);
//                }
//            }

            $this->entityManager->flush();

            $this->addFlash("success", "Successfully updated room info!" );
            return $this->redirect($request->getUri());
        }

        return $this->renderForm("admin/room.html.twig", [
            'form' => $form,
            'room' => $room,
//            'images' => $roomImages
        ]);

    }

    #[Route('admin/offer/add', name: 'admin_offer_add')]
    public function addOffer(Request $request) : Response
    {
        $form = $this->createForm(OfferType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
        }

        return $this->renderForm("admin/offer_add.html.twig", [
            'form' => $form
        ]);
    }

    #[Route('admin/users', name: 'admin_users')]
    public function showUsers(Request $request) : Response
    {
        /** @var User[] $users */
        $users = $this->doctrine->getRepository(User::class)->findAll();

        return $this->renderForm("admin/users.html.twig", [
            'users' => $users
        ]);
    }

}
