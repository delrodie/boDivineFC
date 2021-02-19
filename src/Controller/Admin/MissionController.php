<?php

namespace App\Controller\Admin;

use App\Entity\Mission;
use App\Form\MissionType;
use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/mission")
 */
class MissionController extends AbstractController
{
    /**
     * @Route("/", name="backend_mission_index", methods={"GET"})
     */
    public function index(MissionRepository $missionRepository): Response
    {
        return $this->render('mission/index.html.twig', [
            'missions' => $missionRepository->findAll(),
            'menu' => "accueil",
            'sub_menu' => 'mission'
        ]);
    }

    /**
     * @Route("/new", name="backend_mission_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $mission = new Mission();
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $mission->setStatut(true);
            $entityManager->persist($mission);
            $entityManager->flush();

            $this->addFlash('success', "Succes! La mission a été enregistrée avec succès.");

            return $this->redirectToRoute('backend_mission_index');
        }

        return $this->render('mission/new.html.twig', [
            'mission' => $mission,
            'form' => $form->createView(),
            'menu' => "accueil",
            'sub_menu' => 'mission'
        ]);
    }

    /**
     * @Route("/{id}", name="backend_mission_show", methods={"GET"})
     */
    public function show(Mission $mission): Response
    {
        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_mission_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Mission $mission): Response
    {
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Succes! La mission a été modifiée avec succès.");

            return $this->redirectToRoute('backend_mission_index');
        }

        return $this->render('mission/edit.html.twig', [
            'mission' => $mission,
            'form' => $form->createView(),
            'menu' => "accueil",
            'sub_menu' => 'mission'
        ]);
    }

    /**
     * @Route("/{id}", name="backend_mission_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Mission $mission): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mission->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mission);
            $entityManager->flush();

            $this->addFlash('success', "Succes! La mission a été supprimée avec succès.");
        }

        return $this->redirectToRoute('backend_mission_index');
    }
}
