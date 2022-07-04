<?php

namespace App\Form;


use App\Validator\CheckInDate;
use App\Validator\CheckInDateValidator;
use App\Validator\CheckOutDate;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomAvailabilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checkIn', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Check in',
                'attr' => [
                    'min' => $options['checkInMinimum'],
                    'max' => $options['checkInMaximum'],
                    'value' => $options['checkIn'],
                    'id' => 'check_in',
                    'class' => 'availability-date'

                ],
                'constraints' => [new CheckInDate()]
            ])
            ->add('checkOut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Check out',
                'attr' => [
                    'min' => $options['checkOutMinimum'],
                    'max' => $options['checkOutMaximum'],
                    'value' => $options['checkOut'],
                    'id' => 'check_out',
                    'class' => 'availability-date'

                ],
                'constraints' => [new CheckOutDate()]
            ])
            ->add('offerID', TextType::class, [
                'label_attr' => ['hidden' => 'true'],
                'attr' => [
                    'hidden' => true,
                    'readonly' => true,
                    'value' => $options['offerID']
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'form-width-limit w-100', 'id' => 'form_room_availability'],
            'checkInMinimum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 day")) : date("Y-m-d"),
            'checkInMaximum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 year -1 day")) : date("Y-m-d", strtotime("+1 year -2 day")),
            'checkOutMinimum' => date("H") > 3 ? date("Y-m-d", strtotime("+2 day")) : date("Y-m-d", strtotime("+1 day")),
            'checkOutMaximum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 year")) : date("Y-m-d", strtotime("+1 year -1 day")),
            'checkIn' => date("H") > 3 ? date("Y-m-d", strtotime("+1 day")) : date("Y-m-d"),
            'checkOut' => date("H") > 3 ? date("Y-m-d", strtotime("+2 day")) : date("Y-m-d", strtotime("+1 day")),
            'offerID' => null,
        ]);
    }
}
