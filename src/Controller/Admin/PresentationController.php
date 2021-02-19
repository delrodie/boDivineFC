<?php

namespace App\Controller\Admin;

use App\Entity\Presentation;
use App\Form\PresentationType;
use App\Repository\PresentationRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/presentation")
 */
class PresentationController extends AbstractController
{
    /**
     * @Route("/", name="backend_presentation_index", methods={"GET"})
     */
    public function index(PresentationRepository $presentationRepository): Response
    {
        return $this->render('presentation/index.html.twig', [
            'presentations' => $presentationRepository->findAll(),
            'menu' => "accueil",
            'sub_menu' => "presentation"
        ]);
    }

    /**
     * @Route("/new", name="backend_presentation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $presentation = new Presentation();
        $form = $this->createForm(PresentationType::class, $presentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($presentation->getTitre());
            $presentation->setSlug($slug);

            $entityManager->persist($presentation);
            $entityManager->flush();

            $this->addFlash('success', "La présentation a été enregistrée avec succès!");

            return $this->redirectToRoute('backend_presentation_index');
        }

        return $this->render('presentation/new.html.twig', [
            'presentation' => $presentation,
            'form' => $form->createView(),
            'menu' => "presentation",
            'sub_menu' => "presentation"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_presentation_show", methods={"GET"})
     */
    public function show(Presentation $presentation): Response
    {
        return $this->render('presentation/show.html.twig', [
            'presentation' => $presentation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_presentation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Presentation $presentation): Response
    {
        $form = $this->createForm(PresentationType::class, $presentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "La présentation a été modifiée avec succès!");

            return $this->redirectToRoute('backend_presentation_index');
        }

        return $this->render('presentation/edit.html.twig', [
            'presentation' => $presentation,
            'form' => $form->createView(),
            'menu' => "presentation",
            'sub_menu' => "presentation"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_presentation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Presentation $presentation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$presentation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($presentation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_presentation_index');
    }
}
