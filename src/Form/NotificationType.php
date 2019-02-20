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
            ->add('type')
            ->add('priority')
            ->add(
                'start',
                DateTimeType::class,
                [
                    'label'       => 'Start',
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'attr' => [
                        'class' => 'datetime-picker',
                        'data-min-date' => $now->format('Y-m-d'),
                        'data-min-time' => $now->format('G:i')],
                ]
            )
            ->add(
                'finish',
                null,
                [
                    'label'       => 'End',
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'attr' => [ 'class' => 'datetime-picker']
                ]
            );
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
