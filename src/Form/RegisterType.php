<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 8
                    ])
                ]
            ])
            // ->add('check_password', PasswordType::class, [
            //     'constraints' => [
            //         new EqualTo([
            //             'propertyPath' => 'password'
            //         ])
            //     ]
            // ])
            ->add('nom', TextType::class, [
                'attr' => [ 
                    'maxlength' => '255'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 255
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'attr' => [ 
                    'maxlength' => '255'
                ],                
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 255
                    ])
                ]
            ])
            ->add('adresse', TextareaType::class, [
                'attr' => [ 
                    'maxlength' => '255'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 255
                    ])
                ]
            ])
            ->add('certificat', FileType::class, [
                'label' => 'Certificat d\'autoritsation',
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'application/pdf',
                        ]
                    
                    ])
                ],
                'attr' => [
                    'class' => 'form-field',
                    'placeholder' => 'Fichier justificatif'
                    ]
            ] )
            ->add('portrait', FileType::class, [
                'label' => 'Portrait photo',
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ]
                    
                    ])
                ],
                'attr' => [
                    'class' => 'form-field',
                    'placeholder' => 'fichier image'
                    ]
            ] )
            ->add('submit', SubmitType::class, [
                'label' => 'Cr??er un compte',
                'attr' => [
                    'class' => 'btn-success'
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
