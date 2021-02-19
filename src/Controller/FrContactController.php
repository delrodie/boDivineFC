<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contact")
 */
class FrContactController extends AbstractController
{
    /**
     * @Route("/", name="frontend_contact_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('frontend/contact.html.twig',[
            'menu' => "contact"
        ]);
    }
}