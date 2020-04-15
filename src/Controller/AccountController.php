<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * 
     *@Route("/logout", name="account_logout")
     * 
     */
    public function logout()
    {
    }
    /**
     * @Route("/register", name="account_register")
     *
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "Votre compte a bien été créé");
            $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     *@IsGranted("ROLE_USER") 
     *@Route("/account/profile", name="account_profile")
     * 
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'motification succés');
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/account/password-update", name="account_password")
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {

            $passwordUpdate = new PasswordUpdate();
        
            $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
            $form->handleRequest($request);

            $user = $this->getUser();

            if ($form->isSubmitted() && $form->isValid()) {
                //verify old password
                if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                    $form->get('oldPassword')->addError(new FormError("mauvais mot de passe"));
                } else {
                    $hash = $encoder->encodePassword($user, $passwordUpdate->getNewPassword());
                    $user->setHash($hash);
                    $manager->persist($user);
                    $manager->flush();

                    $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                    return $this->redirectToRoute('homepage');
                }
            }

            return $this->render('account/password.html.twig', [
                'form' => $form->createView()    
            ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/account", name="account_index")
     *
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/account/bookings", name="account_bookings")
     *
     * @return void
     */
    public function bookings() 
    {
        return $this->render("account/bookings.html.twig", [
                     
        ]);
    }
}
