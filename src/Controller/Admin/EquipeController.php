<?php

namespace App\Controller\Admin;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/equipe")
 */
class EquipeController extends AbstractController
{
    private $gestionMedia;

    public function __construct(GestionMedia $gestionMedia)
    {
        $this->gestionMedia = $gestionMedia;
    }

    /**
     * @Route("/", name="backend_equipe_index", methods={"GET"})
     */
    public function index(EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAll(),
            'menu' => "presentation",
            'sub_menu' => "equipe"
        ]);
    }

    /**
     * @Route("/new", name="backend_equipe_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Vérification de non attribution du numéro d'ordre
            $verifOrdre = $this->getDoctrine()->getRepository(Equipe::class)->findOneBy(['ordre'=>$equipe->getOrdre()]);
            if ($verifOrdre){
                $this->addFlash('danger', "Échec, ce numero d'ordre a déjà été attribué. Merci de le modifier");
                return $this->redirectToRoute('backend_equipe_new');
            }

            $slugify = new Slugify();
            $slug = $slugify->slugify($equipe->getNom()).'-'.$slugify->slugify($equipe->getFonction());

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'equipe');

                $equipe->setMedia($media);
            }else{
                $this->addFlash('danger', "<strong>Erreur!</strong> Vous devez telecharger une photo");

                return $this->redirectToRoute('backend_equipe_new');
            }
            $equipe->setSlug($slug);
            $entityManager->persist($equipe);
            $entityManager->flush();

            $this->addFlash('success', "Succes! L'equipe a été enregistrée avec succès.");

            return $this->redirectToRoute('backend_equipe_index');
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
            'menu' => "presentation",
            'sub_menu' => "equipe"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_equipe_show", methods={"GET"})
     */
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backend_equipe_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Equipe $equipe): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slugify = new Slugify();
            $slug = $slugify->slugify($equipe->getNom()).'-'.$slugify->slugify($equipe->getFonction());
            $equipe->setSlug($slug);

            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'equipe');

                // Supression de l'ancien fichier
                $this->gestionMedia->removeUpload($equipe->getMedia(), 'equipe');

                $equipe->setMedia($media);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_equipe_index');
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
            'menu' => "presentation",
            'sub_menu' => "equipe"
        ]);
    }

    /**
     * @Route("/{id}", name="backend_equipe_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Equipe $equipe): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $media = $equipe->getMedia();
            $entityManager->remove($equipe);
            $entityManager->flush();

            // Supression de l'ancien fichier
            $this->gestionMedia->removeUpload($media, 'equipe');

            $this->addFlash('success', "Succes! La biographie a été supprimée avec succès");
        }

        return $this->redirectToRoute('backend_equipe_index');
    }
}
