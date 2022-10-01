<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('surname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('imageUrl', FileType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'empty_data' => '',
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new All([
                        new File([
                            "mimeTypes" => [
                                "image/png",
                                "image/jpg",
                                "image/jpeg",
                                "image/gif"
                            ],
                            "mimeTypesMessage" => "This file is not a valid image."
                        ])
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
