<?php

namespace App\Controller;

use App\Entity\Domaine;
use App\Entity\Equipe;
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
     * cf: https://nouvelle-techno.fr/actualites/live-coding-creer-un-fichier-sitemap-xml-avec-symfony-4
     *
     * @Route("/sitemap.xml", name="app_sitemap", defaults={"_format"="xml"})
     */
    public function sitemap(Request $request)
    {
        // Nous recuperons le nom d'hôte
        $hostname= $request->getSchemeAndHttpHost();

        // On initialise un tableau pour lister les Urls
        // Puis ajoute les urls statiques
        $urls = [];
        $medias = [
            'loc'=> '/assets/images/logo-divine.png',
            'title' => "Divine Finances Conseils"
        ];

        $urls[] = [
            'loc' => $this->generateUrl('app_home'),
            'image' => $medias
        ];
        $urls[] = [
            'loc' => $this->generateUrl('frontend_contact_index'),
            'image' => $medias
        ];
        $urls[] = [
            'loc' => $this->generateUrl('frontend_domaine_index'),
            'image' => $medias
        ];
        $urls[] = [
            'loc' => $this->generateUrl('frontend_equipe_index'),
            'image'=>$medias
        ];
        $urls[] = [
            'loc' => $this->generateUrl('frontend_presentation'),
            'image' => $medias
        ];

        // On ajoute les urls de domaine
        $metiers = $this->getDoctrine()->getRepository(Metier::class)->findBy(['statut'=>true]);
        foreach ($metiers as $metier){
            $urls[] = ['loc' => $this->generateUrl('frontend_metier_show', ['slug'=>$metier->getSlug()])];
        }

        // On ajoute les urls d'equipe
        $equipes = $this->getDoctrine()->getRepository(Equipe::class)->findAll();
        foreach ($equipes as $equipe){
            $images = [
                'loc' => 'uploads/equipe/'.$equipe->getMedia(),
                'title' => $equipe->getNom()
            ];
            $urls[] = [
                'loc' => $this->generateUrl('frontend_equipe_show', ['slug'=>$equipe->getSlug()]),
                'image' => $images
            ];
        }

        // Fabrication de la réponse XML
        $response = new Response(
            $this->renderView('home/sitemap.html.twig',[
                'urls' => $urls,
                'hostname' => $hostname
            ]), 200
        );

        // Ajout des entêtes
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * @Route("/robots.txt", name="app_robot")
     */
    public function robot()
    {
        return $this->render('home/robots.html.twig');
    }

    /**
     * @Route("/maintenance/", name="app_maintenance", methods={"GET","POST"})
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
