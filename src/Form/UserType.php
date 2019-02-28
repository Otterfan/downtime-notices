<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uid',null, ['label' => 'uid'])
            ->add('last_name')
            ->add('first_name')
            ->add('email')
            ->add('roles',
                ChoiceType::class, [
                    'choices' => ['Admin' => 'ROLE_ADMIN', 'User' => 'ROLE_USER'],
                    'expanded' => true,
                    'multiple' => true
                ]
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
