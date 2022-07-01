<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image', FileType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'empty_data' => '',
                'multiple' => true
            ])
            ->add('video', CollectionType::class, [
                'entry_type' => VideoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'prototype' => true,
                'label' => false,
                'empty_data' => ''
            ])
            ->add('user');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
