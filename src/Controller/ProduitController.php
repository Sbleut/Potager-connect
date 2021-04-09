<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Form\ProduitType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{

    /**
     * @Route("/produit/create", name="creer-produit")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function createProduit(Request $r): Response
    {
        $user = $this->getUser();

        $produit = new Produit();

        $formProduit = $this->createForm(ProduitType::class, $produit);
        $formProduit->handleRequest($r);

        if ($formProduit->isSubmitted() && $formProduit->isValid()) {

            $produit->setProducteur($user);

            $photo = $formProduit->get('photo')->getData();
            // On définit le nom du fichier
            $fileName =  uniqid() . '.' . $photo->guessExtension();

            try {
                // On déplace le fichier
                $photo->move($this->getParameter('produit_photo_directory'), $fileName);
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
            'form' => $formProduit->createView(),
        ]);
    }


    /**
     * @Route("/produit/{id}", name="produit")
     */
    public function afficherUnProduit($id): Response
    {
        $repository = $this->getDoctrine()->getRepository(produit::class);
        $produit = $repository->find($id);

        if (empty($produit)) throw new NotFoundHttpException();

        return $this->render('produit.html.twig', [
            'produit' => $produit
        ]);
    }

    /**
     * @Route("/produits", name="produits")
     */
    public function afficherTousLesArticle(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $repository->findAll();



        return $this->render('produits.html.twig', [
            'produits' => $produits
        ]);
    }

    /**
     * @Route("/gerer-produits", name="gerer_produits")
     */
    public function gererProduits(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Produit::class);
        $produit = $repository->findAll();

        return $this->render('produit/gerer-produit.html.twig', [
            'produit' => $produit
        ]);
    }

    /**
     * @Route("/modifier-un-produit/{id}", name="modifier_produit")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function modifierProduit($id, Request $r): Response
    {      
        $repo = $this->getDoctrine()->getRepository(produit::class);
        $produit = $repo->find($id);

        if( $produit->getProducteur() != $this->getUser() && !$this->IsGranted('ROLE_ADMIN')){

            return $this->redirectToRoute('accueil');
        }

        $oldPhoto = $produit->getPhoto();

        if (empty($produit)) throw new NotFoundHttpException();

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($r);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('modifier-produit.html.twig', [
                'form' => $form->createView(),
                'oldphoto' => $oldPhoto,
                'produit' => $produit
            ]);
        } else {

            // Je vais déplacer le fichier uploadé
            $photo = $form->get('photo')->getData();

            try {
                $photo->move($this->getParameter('produit_photo_directory'), $oldPhoto);
            } catch (FileException $ex) {
                $form->addError(new FormError('Une erreur est survenue pendant l\'upload du fichier : ' . $ex->getMessage()));
                throw new Exception('File upload error');
            }

            $produit->setphoto($oldPhoto);

            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();

            return $this->redirect('/produit/' . $produit->getId());
        }
    }

    /**
     * @Route("/supprimer-un-produit/{id}", name="produit-delete")
     */
    public function supprimerProduit($id): Response
    {

        $repo = $this->getDoctrine()->getRepository(Produit::class);
        $produit = $repo->find($id);

        if (empty($produit)) throw new NotFoundHttpException();

        $user = $this->getUser();

        if ($produit->getProducteur() == $user || $this->IsGranted('ROLE_ADMIN')) {

            $filename = $produit->getPhoto();
            // Je crée une instance de kla classe fileSystem
            $fileSystem = new Filesystem();
            //Je supprime l'photo du dossier
            $fileSystem->remove('%kernel.project_dir%/public/assets/photos/' . $filename);

            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();
        }

        return $this->redirectToRoute('user-retrieve');
    }
}
