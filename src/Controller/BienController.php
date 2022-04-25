<?php

namespace App\Controller;

use App\Entity\Bien;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BienController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $biens = $manager->getRepository(Bien::class)->getLast5();
        return $this->render('bien/home.html.twig', [
            'biens' => $biens,
        ]);
    }
}
