<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'empty_data' => '',
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control mb-5'
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
            ->add('video', CollectionType::class, [
                'entry_type' => VideoType::class,
                'allow_add' => true,
                'allow_delete' => false,
                'required' => false,
                'label' => ''
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
