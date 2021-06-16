<?php

namespace App\Form;

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('template')
            ->add(
                'uptime_robot_code',
                TextType::class,
                [
                    'label' => 'Uptime Robot code',
                    'required' => false
                ]
            )
            ->add(
                'on_homepage',
                CheckboxType::class,
                [
                    'label' => 'Show on homepage?',
                    'attr' => ['class' => 'show-on-homepage'],
                    'required' => false

                ]
            )
            ->add('public_name')
            ->add('public_description');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Application::class,
            ]
        );
    }
}
