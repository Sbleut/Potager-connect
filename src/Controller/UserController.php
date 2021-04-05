<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Form\RegisterType;
use App\Form\ProduitType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function createUser(Request $r, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($r);

        if ($form->isSubmitted() && $form->isValid()) {

            $encodedPassword = $encoder->encodePassword($user, $form->get('password')->getData());

            $user->setPassword($encodedPassword);

            $certificat = $form->get('certificat')->getData();
            // On définit le nom du fichier
            $fileName =  uniqid() . '.' . $certificat->guessExtension();

            try {
                // On déplace le fichier
                $certificat->move($this->getParameter('user_certificat_directory'), $fileName);
            } catch (FileException $ex) {
                $form->addError(new FormError('Une erreur est survenue pendant l\'upload du fichier : ' . $ex->getMessage()));
                throw new Exception('File upload error');
            }

            $user->setCertificat($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        } else {

            return $this->render('user/index.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/user", name="user-retrieve")
     */
    public function retrieveOne(): Response
    {

        $user = $this->getUser();


        if (empty($user)) throw new NotFoundHttpException();

        $formProduit = $this->createForm(ProduitType::class, new Produit());

        $repository = $this->getDoctrine()->getRepository(Produit::class);

        $user_id = $user->getId();

        $produits = $repository->findBy(
            ['id_producteur' => $user_id],
        );

        return $this->render('user/user-retrieve.html.twig', [
            'produits' => $produits,
            'user' => $user,
            'form' => $formProduit->createView()
        ]);
    }

    /**
     * @Route("/users", name="users")
     */
    public function retrieveAll(): Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('user/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/supprimer-user/{id}", name="user-delete")
     */
    public function supprimerUser($id): Response {

        $repo = $this->getDoctrine()->getRepository(Produit::class);
        $produit = $repo->find($id);

        if (empty($produit)) throw new NotFoundHttpException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();

        return $this->redirectToRoute('user-retrieve');
    }
}
