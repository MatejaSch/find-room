<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('pricePerNight')
            ->add('rooms', EntityType::class, [
                'class' => Room::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.capacity', 'ASC');},
                'choice_label' => function ($room) {
                    return "{$room->getNumber()} - Capacity({$room->getCapacity()}) Single Beds({$room->getSingleBed()}) 
                    Double Beds({$room->getSingleBed()})";
                },
                'multiple' => true,
                'expanded' => true]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
            'attr' => ['class' => 'form-width-limit'],
        ]);
    }
}
