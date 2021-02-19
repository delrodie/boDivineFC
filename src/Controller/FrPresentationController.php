<?php

namespace App\Controller;

use App\Entity\Presentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/presentation")
 */
class FrPresentationController extends AbstractController
{
    /**
     * @Route("/", name="frontend_presentation", methods={"GET"})
     */
    public function index()
    {
        return $this->render('frontend/presentation.html.twig',[
            'presentation' => $this->getDoctrine()->getRepository(Presentation::class)->findOneBy([],['id'=>"DESC"]),
            'menu' => "presentation"
        ]);
    }
}