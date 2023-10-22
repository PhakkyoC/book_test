<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private UserPasswordHasherInterface $hasher;

    private EntityManagerInterface $em;


    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $em)
    {
        $this->hasher = $hasher;
        $this->em = $em;
    }

    
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('book_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request){
        if($request->getMethod() == 'POST'){
            try{
                $user = new User();
                $user->setUsername($request->get('username'));
                $password = $this->hasher->hashPassword($user, $request->get('password'));
                $user->setPassword($password);
                $this->em->persist($user);
                $this->em->flush();
                return $this->redirectToRoute('app_login');
            }catch (\Exception $e){
                return $this->render('security/register.html.twig', [
                    'message' => $e->getMessage(),
                    'message_type' => 'error'
                ]);
            }
        }
        else{
            return $this->render('security/register.html.twig', [
            ]);
        }
    }
}
