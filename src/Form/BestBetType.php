<?php

namespace App\Form;

use App\Entity\BestBet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BestBetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $source_url = $builder->getData()->sourceURL() ? $builder->getData()->sourceURL() : null;
        $source_url_options = $source_url ? ['help' => "<a href='$source_url'>$source_url</a>", 'help_html' => true] : [];

        $builder
            ->add('title')
            ->add('text', TextareaType::class,['attr' => ['class' => 'best-bet-text']])
            ->add('link')
            ->add('image')
            ->add('source_type', ChoiceType::class, [
                'choices' => [
                    'AZ list entry' => 'azlist',
                    'FAQ question' => 'faq',
                    'Other' => 'other',
                ],
            ])
            ->add('source_identifier', TextType::class, $source_url_options)
            ->add('terms', CollectionType::class, [
                'entry_type' => BestBetTermType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BestBet::class,
        ]);
    }
}
