<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class, [
            'attr' => [ 
                'maxlength' => '255'
            ],
            'constraints' => [
                new NotBlank(),
                new Email()
            ]
        ])
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
        ->add('message', TextareaType::class, [
            'attr' => [ 
                'maxlength' => '2048'
            ],
            'constraints' => [
                new NotBlank(),
                new Length([
                    'max' => 2048
                ])
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Envoyer',
            'attr' => [
                'class' => 'btn-success'
            ]
        ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
