<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    /**
     * @Route ("/register" , name="register")
     */

    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $formulaire = $this->createForm(RegistrationType::class, $user);

        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $encode = $encoder->encodePassword($user, $user->getPassword());
            $user -> setPassword($encode);
            $user -> setRole('ROLE_USER');

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('login/register.html.twig', [
            'formulaire' => $formulaire->createView()
        ]);
    }

    /**
     * @Route ("/login", name="login")
     */

    public function login()
    {
        return $this->render('login/login.html.twig');
    }

    /**
     * @Route ("/logout", name="logout")
     */
    public function logout()
    {

    }
}
