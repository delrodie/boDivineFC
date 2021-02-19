<?php

namespace App\Controller\Admin;

use App\Entity\Metier;
use App\Form\MetierType;
use App\Repository\MetierRepository;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/metier")
 */
class MetierController extends AbstractController
{
    private $gestionMedia;

    public function __construct(GestionMedia $gestionMedia)
    {
        $this->gestionMedia = $gestionMedia;
    }

    /**
     * @Route("/", name="backend_metier_index", methods={"GET"})
     */
    public function index(MetierRepository $metierRepository): Response
    {
        return $this->render('metier/index.html.twig', [
            'metiers' => $metierRepository->findAll(),
            'menu' => "metier",
            'sub_menu' => "metier"
        ]);
    }

    /**
     * @Route("/new", name="backend_metier_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $metier = new Metier();
        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($metier->getTitre());

            // Verification d'existence du slug
            $verif = $this->getDoctrine()->getRepository(Metier::class)->findOneBy(['slug'=>$slug]);
            if ($verif){
                $this->addFlash('danger', "Ce titre a déjà été enregistré. Merci de le modifier");
                $this->redirectToRoute('backend_metier_new');
            }

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'metier');

                $metier->setMedia($media);
            }else{
                $this->addFlash('danger', "<strong>Erreur!</strong> Vous devez telecharger une photo");

                return $this->redirectToRoute('backend_metier_new');
            }

            $metier->setSlug($slug);
            $metier->setStatut(true);

            $entityManager->persist($metier);
            $entityManager->flush();

            $this->addFlash('success', "Le metier a été enregistré avec succès!");

            return $this->redirectToRoute('backend_metier_index');
        }

        return $this->render('metier/new.html.twig', [
            'metier' => $metier,
            'form' => $form->createView(),
            'menu' => "metier",
            'sub_menu' => "metier"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_metier_show", methods={"GET"})
     */
    public function show(Metier $metier): Response
    {
        return $this->render('metier/show.html.twig', [
            'metier' => $metier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_metier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Metier $metier): Response
    {
        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'metier');

                // Supression de l'ancien fichier
                $this->gestionMedia->removeUpload($metier->getMedia(), 'metier');

                $metier->setMedia($media);
            }

            return $this->redirectToRoute('backend_metier_index');
        }

        return $this->render('metier/edit.html.twig', [
            'metier' => $metier,
            'form' => $form->createView(),
            'menu' => "metier",
            'sub_menu' => "metier"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_metier_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Metier $metier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$metier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($metier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_metier_index');
    }
}
