<?php

declare(strict_types=1);

namespace App\ReadModel\Media\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'mime',
            Type\ChoiceType::class,
            [
                'choices'     => [
                    'jpeg' => 'image/jpeg',
                    'png'  => 'image/png',
                    'gif'  => 'image/gif',
                    'svg'  => 'image/svg+xml',
                ],
                'required'    => false,
                'placeholder' => 'All mimes',
                'attr'        => ['onchange' => 'this.form.submit()'],
            ]
        )->add(
            'tag',
            Type\TextType::class,
            [
                'required' => false,
                'attr'     => [
                    'placeholder' => 'tag',
                    'onchange'    => 'this.form.submit()',
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class'      => Filter::class,
                'method'          => 'GET',
                'csrf_protection' => false,
            ]
        );
    }
}
