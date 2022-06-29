<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Room;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('floor')
            ->add('description')
            ->add('singleBed', null, [
                'attr' => [
                    'class' => 'bed-quantity',
                    'min' => 0,
                    'max' => 10,
                ],
            ])
            ->add('doubleBed', null, [
                'attr' => [
                    'class' => 'bed-quantity',
                    'min' => 0,
                    'max' => 10,
                    ],
            ])
            ->add('offer', EntityType::class, [
                'class' => Offer::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->orderBy('o.capacity', 'ASC')
                        ->orderBy('o.singleBed', 'ASC')
                        ->orderBy('o.doubleBed', 'ASC');
                 },
                'required' => false,
                'choice_label' => 'title',
                'placeholder' => 'Choose offer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
            'attr' => ['class' => 'form-width-limit'],
        ]);
    }
}
