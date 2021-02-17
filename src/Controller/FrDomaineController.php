<?php


namespace App\Controller;


use App\Entity\Domaine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/domaine")
 */
class FrDomaineController extends AbstractController
{
    /**
     * @Route("/", name="frontend_domaine_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('frontend/domaine.html.twig',[
            'domaines' => $this->getDoctrine()->getRepository(Domaine::class)->findAll(),
        ]);
    }
}