<?php

namespace App\Controller\Admin;

use App\Entity\Domaine;
use App\Form\DomaineType;
use App\Repository\DomaineRepository;
use App\Utilities\GestionMedia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/domaine")
 */
class DomaineController extends AbstractController
{
    private $gestionMedia;

    public function __construct(GestionMedia $gestionMedia)
    {
        $this->gestionMedia = $gestionMedia;
    }

    /**
     * @Route("/", name="backend_domaine_index", methods={"GET"})
     */
    public function index(DomaineRepository $domaineRepository): Response
    {
        return $this->render('domaine/index.html.twig', [
            'domaines' => $domaineRepository->findAll(),
            'menu' => "accueil",
            'sub_menu' => "domaine"
        ]);
    }

    /**
     * @Route("/new", name="backend_domaine_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $domaine = new Domaine();
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'domaine');

                $domaine->setMedia($media);
            }else{
                $this->addFlash('danger', "<strong>Erreur!</strong> Vous devez telecharger une photo");

                return $this->redirectToRoute('backend_slide_new');
            }

            $entityManager->persist($domaine);
            $entityManager->flush();

            $this->addFlash('success', "Succes! Le domaine a été enregistré avec succès.");

            return $this->redirectToRoute('backend_domaine_index');
        }

        return $this->render('domaine/new.html.twig', [
            'domaine' => $domaine,
            'form' => $form->createView(),
            'menu' => "accueil",
            'sub_menu' => "domaine"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_domaine_show", methods={"GET"})
     */
    public function show(Domaine $domaine): Response
    {
        return $this->render('domaine/show.html.twig', [
            'domaine' => $domaine,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_domaine_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Domaine $domaine): Response
    {
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'domaine');

                // Supression de l'ancien fichier
                $this->gestionMedia->removeUpload($domaine->getMedia(), 'domaine');

                $domaine->setMedia($media);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Succes! Le domaine a été modifié avec succès.");

            return $this->redirectToRoute('backend_domaine_index');
        }

        return $this->render('domaine/edit.html.twig', [
            'domaine' => $domaine,
            'form' => $form->createView(),
            'menu' => "accueil",
            'sub_menu' => "domaine"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_domaine_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Domaine $domaine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$domaine->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $media = $domaine->getMedia();
            $entityManager->remove($domaine);
            $entityManager->flush();

            // Supression de l'ancien fichier
            $this->gestionMedia->removeUpload($media, 'domaine');

            $this->addFlash('success', "Succes! Le domaine a été supprimé avec succès");
        }

        return $this->redirectToRoute('backend_domaine_index');
    }
}
