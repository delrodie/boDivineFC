<?php
	
	namespace App\Utilities;
	
	use App\Entity\User;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
	use Symfony\Component\Security\Core\Security;
	
	class GestionSecurity
	{
		private $entityManager;
		private $passwordEncoder;
		private $security;
		
		public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, Security $security)
		{
			$this->entityManager = $entityManager;
			$this->passwordEncoder = $passwordEncoder;
			$this->security = $security;
		}
		
		/**
		 * @return bool
		 */
		public function initialisationUser(): bool
		{
			$user = new User();
			$user->setEmail('delrodieamoikon@gmail.com');
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'divinefinances'));
			$user->setRoles(['ROLE_SUPER_ADMIN']);
			
			$this->entityManager->persist($user);
			$this->entityManager->flush();
			
			return true;
		}
		
		/**
		 * @return bool
		 */
		public function connexion(): bool
		{
			$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->security->getUser()->getUsername()]);
			
			$nombre_connexion = $user->getConnexion();
			$user->setConnexion($nombre_connexion+1);
			$user->setLastConnectedAt(new \DateTime());
			
			$this->entityManager->flush();
			
			return true;
		}
	}
