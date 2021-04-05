<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Form\ProduitType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController {

    /**
     * @Route("/produit/create/{user_id}", name="creer-produit")
     */
    public function createProduit($user_id, Request $r): Response {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($user_id);

        if (empty($user)) throw new NotFoundHttpException();

        $produit = new Produit();

        $formProduit = $this->createForm(ProduitType::class, $produit);
        $formProduit->handleRequest($r);

        if($formProduit->isSubmitted() && $formProduit->isValid()) {

            $produit->setProducteur($user);

            $image = $formProduit->get('photo')->getData();
            // On définit le nom du fichier
            $fileName =  uniqid() . '.' . $image->guessExtension();

            try {
                // On déplace le fichier
                $image->move($this->getParameter('produit_photo_directory'), $fileName);
            } catch (FileException $ex) {
                $formProduit->addError(new FormError('Une erreur est survenue pendant l\'upload du fichier : ' . $ex->getMessage()));
                throw new Exception('File upload error');
            }

            $produit->setPhoto($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
             
        }

        return $this->render('user/user-retrieve.html.twig', [
            'user' => $user,
            'form' => $formProduit->createView()
        ]); 
        
        // $this->redirect('/user/' . $user->getId());
    }


    /**
     * @Route("/produit/{id}", name="produit")
     */
    public function afficherUnProduit($id): Response {
        $repository = $this->getDoctrine()->getRepository(produit::class);
        $produit = $repository->find($id);

        if (empty($produit)) throw new NotFoundHttpException();

        return $this->render('produit/index.html.twig', [
            'produit' => $produit
        ]);
    }

    /**
     * @Route("/produits", name="produits")
     */
    public function afficherTousLesArticle(): Response {
        $repository = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $repository->findAll();



        return $this->render('produits.html.twig', [
            'produits' => $produits
        ]);
    }

    /**
     * @Route("/gerer-produits", name="gerer_produits")
     */
    public function gererProduits(): Response {
        $repository = $this->getDoctrine()->getRepository(Produit::class);
        $produit = $repository->findAll();

        return $this->render('produit/gerer-produit.html.twig', [
            'produit' => $produit
        ]);
    }

    /**
     * @Route("/creer-un-produit", name="creer_produit")
     */
    public function creerProduit(Request $r): Response {

        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($r);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('produit/creer-produit.html.twig', [
                'form' => $form->createView()
            ]);
        } else {

            // Je vais déplacer le fichier uploadé

            // On récupère l'image
            $image = $form->get('image')->getData();
            // On définit le nom du fichier
            $fileName =  uniqid() . '.' . $image->guessExtension();

            try {
                // On déplace le fichier
                $image->move($this->getParameter('produit_image_directory'), $fileName);
            } catch (FileException $ex) {
                $form->addError(new FormError('Une erreur est survenue pendant l\'upload du fichier : ' . $ex->getMessage()));
                throw new Exception('File upload error');
            }

            $produit->setPhoto($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();

            return $this->redirect('/produit/' . $produit->getId());
        }
    }

    /**
     * @Route("/modifier-un-produit/{id}", name="modifier_produit")
     */
    public function modifierProduit($id, Request $r): Response {

        $repo = $this->getDoctrine()->getRepository(produit::class);
        $produit = $repo->find($id);

        $oldImage = $produit->getImage();

        if (empty($produit)) throw new NotFoundHttpException();

        $form = $this->createForm(produitType::class, $produit);

        $form->handleRequest($r);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('produit/modifier-produit.html.twig', [
                'form' => $form->createView(),
                'oldImage' => $oldImage,
                'id' => $produit->getId()
            ]);
        } else {

            // Je vais déplacer le fichier uploadé
            $image = $form->get('image')->getData();

            try {
                $image->move($this->getParameter('produit_image_directory'), $oldImage);
            } catch (FileException $ex) {
                $form->addError(new FormError('Une erreur est survenue pendant l\'upload du fichier : ' . $ex->getMessage()));
                throw new Exception('File upload error');
            }

            $produit->setImage($oldImage);

            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();

            return $this->redirect('/produit/' . $produit->getId());
        }
    }

    /**
     * @Route("/supprimer-un-produit/{id}", name="produit-delete")
     */
    public function supprimerProduit($id): Response {

        $repo = $this->getDoctrine()->getRepository(Produit::class);
        $produit = $repo->find($id);

        if (empty($produit)) throw new NotFoundHttpException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();

        return $this->redirectToRoute('user-retrieve');
    }
}
