<?php

namespace App\Form;

use App\Entity\Loan;
use App\Entity\User;
use App\Entity\CDRom;
use App\Entity\Livre;;
use App\Repository\LivreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('createdAt')
            ->add('udatedAt')
            ->add('status')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getFirstName();
                }
            ])
            /* 
            ->add('livres', EntityType::class, [
                'class' => Livre::class,
                'query_builder' => function (LivreRepository $livre) {
                    return $livre->createQueryBuilder('l')
                        ->orderBy('l.title', 'ASC');
                },
                'choice_label' => 'title',
            ])  */
            
            ->add('livres', EntityType::class, [
                'class' => Livre::class,
                'choice_label' => function ($livre) {
                    return $livre->getTitle();
                }
            ])

            ->add('cdrom', EntityType::class, [
                'class' => CDRom::class,
                'choice_label' => function ($cdrom) {
                    return $cdrom->getTitle();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Loan::class,
        ]);
    }
}
