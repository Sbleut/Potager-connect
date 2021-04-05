<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nature', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('variete', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('categorie', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('traitement', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => 'photo',
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ]
                    
                    ])
                ],
                'attr' => [
                    'class' => 'form-field',
                    'placeholder' => 'Fichier justificatif'
                    ]
            ])
            ->add('prix', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter un produit',
                'attr' => [
                    'class' => 'button'
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
