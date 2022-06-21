<?php

namespace App\Controller;

use App\Entity\Room;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    #[Route('/offers', name: 'app_offers')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $capacity = $request->query->get('capacity');
        $doctrine->getRepository(Room::class)->findAllOffers();



        return $this->render("offer/offers.html.twig");
    }
}