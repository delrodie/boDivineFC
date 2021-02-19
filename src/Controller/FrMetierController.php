<?php


namespace App\Controller;


use App\Entity\Metier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/metier")
 */
class FrMetierController extends AbstractController
{
    /**
     * @Route("/", name="frontend_metier_menu", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('menu/metier_horizontal.html.twig',[
            'metiers' => $this->getDoctrine()->getRepository(Metier::class)->findBy(['statut'=>true])
        ]);
    }

    /**
     * @Route("/{slug}", name="frontend_metier_show", methods={"GET"})
     */
    public function show(Metier $metier)
    {
        return $this->render('frontend/metier_show.html.twig',[
            'metier' => $metier,
            'menu' => "metier",
            'metiers' => $this->getDoctrine()->getRepository(Metier::class)->findBy(['statut'=>true])
        ]);
    }
}