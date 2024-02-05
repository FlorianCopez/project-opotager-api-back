<?php

namespace App\Form;

use App\Entity\Garden;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GardenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => '* Titre',
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => '* Description',
            ])
            ->add('location', TextType::class, [
                'required' => true,
                'label' => '* Adresse',
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => '* Ville',
            ])
            ->add('postalCode', TextType::class, [
                'required' => true,
                'label' => '* Code postal',
            ])
            ->add('state', TextType::class, [
                'required' => true,
                'label' => '* Etat du jardin',
            ])
            ->add('surface', NumberType::class, [
                'required' => true,
                'html5' => true,
                'label' => '* Surface en m2',
            ])
            ->add('water', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'expanded' => true,
                'required' => true,
                'label' => '* Point d\'eau disponible ?'
            ])
            ->add('tool', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'expanded' => true,
                'required' => true,
                'label' => '* Outils à disposition ?'
            ])
            ->add('shed', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'expanded' => true,
                'required' => true,
                'label' => '* Abri de jardin ?'
            ])
            ->add('checked', ChoiceType::class, [
                'choices' => [
                    'Valider' => 'validate',
                    'Refuser' => 'reject'
                ],
                'expanded' => true,
                'required' => true,
                'label' => '* Valider l\'annonce ?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Garden::class,
        ]);
    }
}
