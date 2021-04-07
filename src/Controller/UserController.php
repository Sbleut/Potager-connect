<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Form\RegisterType;
use App\Form\ProduitType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
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

            // GESTION DU CERTIFICAT /////////

            $certificat = $form->get('certificat')->getData();
            // On définit le nom du fichier
            $fileName1 =  uniqid() . '.' . $certificat->guessExtension();

            try {
                // On déplace le fichier
                $certificat->move($this->getParameter('user_certificat_directory'), $fileName1);
            } catch (FileException $ex) {
                $form->addError(new FormError('Une erreur est survenue pendant l\'upload du fichier : ' . $ex->getMessage()));
                throw new Exception('File upload error');
            }

            $user->setCertificat($fileName1);

            // GESTION DE LA PHOTO UTILISATEUR //////////

            $portrait = $form->get('portrait')->getData();

            if (empty($portrait)) {
                $user->setPortrait('unknown-portrait.webp');
            }

            $fileName2 = uniqid() . '.' . $portrait->guessExtension();

            try {
                // On déplace le fichier
                $portrait->move($this->getParameter('user_portrait_directory'), $fileName2);
            } catch (FileException $ex) {
                $form->addError(new FormError('Une erreur est survenue pendant l\'upload du fichier : ' . $ex->getMessage()));
                throw new Exception('File upload error');
            }

            $user->setPortrait($fileName2);


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
     * @Route("/user/profil", name="user-profil")
     */
    public function retrieveProfil(): Response
    {

        $user = $this->getUser();


        if (empty($user)) throw new NotFoundHttpException();

        $formProduit = $this->createForm(ProduitType::class, new Produit());

        $repository = $this->getDoctrine()->getRepository(Produit::class);

        $user_id = $user->getId();

        $produits = $repository->findBy(
            ['id_producteur' => $user_id],
        );

        return $this->render('user/user-profil.html.twig', [
            'produits' => $produits,
            'user' => $user,
            'form' => $formProduit->createView()
        ]);
    }

    /**
     * @Route("/user/{id}", name="user-retrieve")
     */
    public function retrieveOne($id = null): Response
    {

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);


        if (empty($user)) throw new NotFoundHttpException();

        $repository = $this->getDoctrine()->getRepository(Produit::class);

        $user_id = $user->getId();

        $produits = $repository->findBy(
            ['id_producteur' => $user_id],
        );

        return $this->render('user/user-retrieve.html.twig', [
            'produits' => $produits,
            'user' => $user,
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
    public function supprimerUser($id): Response
    {

        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository(User::class);
        $userDel = $repo->find($id);

        if (empty($produit)) {
            return $this->redirectToRoute('register');
        }


        if ($user == $userDel || $this->IsGranted('ROLE_ADMIN')) {
            /**
             * Je gère la suppression du dossier "uploads" ou l'image est stockée
             */
            //Je récupère le nom de l'image
            $filename = $userDel->getPortrait();
            // Je crée une instance de kla classe fileSystem
            $fileSystem = new Filesystem();
            //Je supprime l'image du dossier
            $fileSystem->remove('%kernel.project_dir%/public/assets/portraits/' . $filename);


            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user-retrieve');
    }
}
