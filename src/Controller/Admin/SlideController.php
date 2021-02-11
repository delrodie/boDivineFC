<?php

namespace App\Controller\Admin;

use App\Entity\Slide;
use App\Form\SlideType;
use App\Repository\SlideRepository;
use App\Utilities\GestionMedia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/slide")
 */
class SlideController extends AbstractController
{
    private $gestionMedia;

    public function __construct(GestionMedia $gestionMedia)
    {
        $this->gestionMedia = $gestionMedia;
    }

    /**
     * @Route("/", name="backend_slide_index", methods={"GET"})
     */
    public function index(SlideRepository $slideRepository): Response
    {
        return $this->render('slide/index.html.twig', [
            'slides' => $slideRepository->findAll(),
            'menu' => "accueil",
            'sub_menu' => "slide"
        ]);
    }

    /**
     * @Route("/new", name="backend_slide_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $slide = new Slide();
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'slide');

                $slide->setMedia($media);
            }else{
                $this->addFlash('danger', "<strong>Erreur!</strong> Vous devez telecharger une photo");

                return $this->redirectToRoute('backend_slide_new');
            }
            //dd($slide);
            $slide->setStatut(true);

            $entityManager->persist($slide);
            $entityManager->flush();

            $this->addFlash('success', "Succes! Le slide a été enregistré avec succès.");

            return $this->redirectToRoute('backend_slide_index');
        }

        return $this->render('slide/new.html.twig', [
            'slide' => $slide,
            'form' => $form->createView(),
            'menu' => "accueil",
            'sub_menu' => "slide"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_slide_show", methods={"GET"})
     */
    public function show(Slide $slide): Response
    {
        return $this->render('slide/show.html.twig', [
            'slide' => $slide,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_slide_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Slide $slide): Response
    {
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'slide');

                // Supression de l'ancien fichier
                $this->gestionMedia->removeUpload($slide->getMedia(), 'slide');

                $slide->setMedia($media);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Succes! Le slide a été modifié avec succès.");

            return $this->redirectToRoute('backend_slide_index');
        }

        return $this->render('slide/edit.html.twig', [
            'slide' => $slide,
            'form' => $form->createView(),
            'menu' => "accueil",
            'sub_menu' => "slide"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_slide_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Slide $slide): Response
    {
        if ($this->isCsrfTokenValid('delete'.$slide->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $media = $slide->getMedia();
            $entityManager->remove($slide);
            $entityManager->flush();

            // Supression de l'ancien fichier
            $this->gestionMedia->removeUpload($media, 'slide');

            $this->addFlash('success', "Succes! Le slide a été supprimé avec succès");
        }

        return $this->redirectToRoute('backend_slide_index');
    }
}
