<?php

namespace App\Form;

use App\Entity\Notification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $now = new \DateTime('now', new \DateTimeZone('America/New_York'));

        $builder
            ->add('text')
            ->add(
                'start',
                DateTimeType::class,
                ['label' => 'Start']
            )
            ->add('finish', null, ['label' => 'End']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Notification::class,
            ]
        );
    }
}
