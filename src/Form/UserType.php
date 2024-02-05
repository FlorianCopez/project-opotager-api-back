<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label' => '* Nom d\'utilisateur'
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => '* Email'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => $options['isNewUser'],
                'mapped' => $options['isNewUser'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                "choices" => [
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                "expanded" => true,
                'multiple' => true,
            ])
            ->add('phone', TelType::class, [
                'required' => true,
                'label' => '* Téléphone'
            ])
            ->add('avatar', UrlType::class, [
                'label' => 'Url de l\'avatar',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isNewUser' => true,
        ]);
    }
}
