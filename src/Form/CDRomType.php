<?php

namespace App\Form;

use App\Entity\CDRom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CDRomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('cote', TextType::class)
            ->add('availability', CheckboxType::class)
            ->add('picture', TextType::class)
            ->add('authtor', TextType::class)
            ->add('bail', NumberType::class)
            //->add('loans')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CDRom::class,
        ]);
    }
}
