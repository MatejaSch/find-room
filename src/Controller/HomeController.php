<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $room = $doctrine->getRepository(Room::class)->findAllRoomCapacities();
        $today = date("Y-m-d");
        $yearFromToday = date('Y-m-d', strtotime('+1 year'));

        return $this->render("home/home.html.twig", [
            'capacities' => $room,
            'today' => $today,
            'yearFromToday' => $yearFromToday
        ]);
    }
}
