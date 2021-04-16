<?php

namespace App\Controller;

use App\Entity\Equipe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FrEquipeController
 * @Route("/notre-equipe")
 */
class FrEquipeController extends AbstractController
{
    /**
     * @Route("/", name="frontend_equipe_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('frontend/equipes.html.twig',[
            'equipes' => $this->getDoctrine()->getRepository(Equipe::class)->findAll(),
            'menu' => 'presentation'
        ]);
    }
}