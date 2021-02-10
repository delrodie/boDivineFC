<?php


namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend")
 */
class BackendController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard", methods={"GET"})
     */
    public function dashboard(Request $request): Response
    {
        return $this->render("home/dashboard.html.twig",[
            'menu' => 'dashboard',
            'sub_menu' => 'backend'
        ]);
    }
}