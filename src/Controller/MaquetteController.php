<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/maquette")
 */
class MaquetteController extends AbstractController
{
    /**
     * @Route("/", name="maquette_index")
     */
    public function index(): Response
    {
        return $this->render('maquette/index.html.twig', [
            'menu' => 'accueil',
        ]);
    }
}
