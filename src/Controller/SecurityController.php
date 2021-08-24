<?php

namespace App\Controller;

use App\Form\ResetPwdFormType;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/forgot_pwd", name="app_forgot_pwd")
     */
    public function forgotPwd(AdminRepository $adminRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em)
    {

        // vérification de l'existence de l'email
        if(isset($_POST['submitEmail']))
        {
            // on recherche s'il existe un user avec l'email saisi
            $user= $adminRepository->findOneBy(['email' => $_POST['emailAdmin']]);
            if(!$user)
            {
                $this->addFlash('error','Email inconnu');
                return $this->redirectToRoute('app_forgot_pwd');
            }

            return $this->render('security/reset_pwd.html.twig',[
                'user' => $user,
            ]);

        }

        // enregistrement du nouveau mot de passe
        if(isset($_POST['submitPwd']))
        {
            // on récupère le user
            $user = $adminRepository->findOneBy(['username' => $_POST['username']]);
            // on vérifie si le mot de passe confirmé est différent de celui saisi en premier
            if($_POST['pwd2'] !== $_POST['pwd1'])
            {
                $error = 'Le mot de passe confirmé ne correspond pas au nouveau mot de passe saisi. Veuillez le saisir de nouveau.';
                return $this->render('security/reset_pwd.html.twig',[
                    'user' => $user,
                    'errorPwd' => $error
                ]);
            }
            // sinon, on modifie le mot de passe de l'user
            $user->setPassword($passwordHasher->hashPassword($user, $_POST['pwd1']));
            // on update la BD
            $em->persist($user);
            $em->flush();

            $success = 'Votre nouveau mot de passe a bien été enregistré. Vous pouvez vous connecter.';
            

            $this->addFlash('success','Votre nouveau mot de passe a bien été enregistré. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');

        }

        return $this->render('security/forgot_pwd.html.twig');
    }
}
