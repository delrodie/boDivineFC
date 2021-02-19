<?php

namespace App\Controller;

use App\Entity\Domaine;
use App\Entity\Metier;
use App\Entity\Mission;
use App\Entity\Newsletter;
use App\Entity\Slide;
use App\Form\NewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {

        $slides = $this->getDoctrine()->getRepository(Slide::class)->findAll();
        $domaines = $this->getDoctrine()->getRepository(Domaine::class)->findAll();

        // Si les slides et domaines n'existent pas alors rediriger vers la maintenance;
        if (!$slides || !$domaines)
            return $this->redirectToRoute('app_maintenance');

        return $this->render('home/index.html.twig', [
            'menu' => "accueil",
            'slides' => $slides,
            'domaines' => $domaines,
            'mission' => $this->getDoctrine()->getRepository(Mission::class)->findOneBy(['statut'=>true],["id"=>"DESC"]),
            'metiers' => $this->getDoctrine()->getRepository(Metier::class)->findBy(['statut'=>true])
        ]);
    }

    /**
     * @Route("/maintenance", name="app_maintenance", methods={"GET","POST"})
     */
    public function maintenance(Request $request)
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $verif = $this->getDoctrine()->getRepository(Newsletter::class)->findOneBy(['email'=>$newsletter->getEmail()]);
            if ($verif){
                $this->addFlash('danger', "Echec vous êtes déjà inscrit");
                return $this->redirectToRoute('app_home');
            }

            $entityManager->persist($newsletter);
            $entityManager->flush();

            $this->addFlash('success', "Votre email a bien été enregistré");

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/maintenance.html.twig', [
            'newsletter' => $newsletter,
            'form' => $form->createView(),
        ]);
    }
}
