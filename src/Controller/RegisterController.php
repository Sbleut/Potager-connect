<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function createUser(Request $r, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(RegisterType::class);

        $form->handleRequest($r);

        if ($form->isSubmitted() && $form->isValid()){

            $user = new User();

            $encodedPassword = $encoder->encodePassword($user, $form['password']);

            $user->setEmail($form['email']);
            $user->setPassword($encodedPassword);
            $user->setNom($form['nom']);
            $user->setPrenom($form['prenom']);
            $user->setAdresse($form['adresse']);
            $user->setCertificat($form['certificat']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');

        } else {

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
        }
    }
}
