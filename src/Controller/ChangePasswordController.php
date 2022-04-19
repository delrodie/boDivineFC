<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/change/password")
 */
class ChangePasswordController extends AbstractController
{
	private $passwordEncoder;
	
	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}
	
    /**
     * @Route("/", name="admin_change_password")
     */
    public function index(Request $request): Response
    {
		$user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUsername()]);
		$form = $this->createForm(ChangePasswordType::class, $user);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()){ //dd($form->get('password')->getData());
			$user->setPassword($this->passwordEncoder->encodePassword(
				$user,
				$form->get('password')->getData()
			));
			$this->getDoctrine()->getManager()->flush();
			
			$this->addFlash('success', "Votre mot de passe a bien été modifiée");
			
			return $this->redirectToRoute('app_logout');
		}
		
        return $this->render('change_password/index.html.twig', [
	        'resetForm' => $form->createView(),
        ]);
    }
}
