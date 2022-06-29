<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Validator\CheckInDate;
use App\Validator\CheckInDateValidator;
use App\Validator\CheckOutDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdBy', TextType::class, [
                'data' => $options['user'],
                'attr' => ['readonly' => true],
                'mapped' => false
            ])
            ->add('checkIn', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Check in',
                'attr' => [
                    'min' => $options['checkInMinimum'],
                    'max' => $options['checkInMaximum'],
                    'value' => $options['checkIn'],
                    'id' => 'check_in',
                    'class' => 'date'
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
                    'class' => 'date'
                ],
                'constraints' => [new CheckOutDate()]
            ])
            ->add('offer', TextType::class, [
                'mapped' => false,
                'data' => $options['offer'],
                'attr' => ['readonly' => true]
            ])
            ->add('price', TextType::class, [
                'mapped' => false,
                'data' => $options['price'],
                'attr' => [
                    'readonly' => true,
                    'id' => 'price'
                    ]
            ])
            ->add('offerID', TextType::class, [
                'label_attr' => ['hidden' => 'true'],
                'attr' => [
                    'hidden' => true,
                    'readonly' => true,
                    'value' => $options['offerID']
                ],
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'allow_extra_fields' => true,
            'attr' => ['class' => 'form-width-limit my-3', 'id' => 'form_reservation'],
            'checkInMinimum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 day")) : date("Y-m-d"),
            'checkInMaximum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 year -1 day")) : date("Y-m-d", strtotime("+1 year -2 day")),
            'checkOutMinimum' => date("H") > 3 ? date("Y-m-d", strtotime("+2 day")) : date("Y-m-d", strtotime("+1 day")),
            'checkOutMaximum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 year")) : date("Y-m-d", strtotime("+1 year -1 day")),
            'checkIn' => date("H") > 3 ? date("Y-m-d", strtotime("+1 day")) : date("Y-m-d"),
            'checkOut' => date("H") > 3 ? date("Y-m-d", strtotime("+2 day")) : date("Y-m-d", strtotime("+1 day")),
            'user' => null,
            'offer' => null,
            'price' => null,
            'offerID' => null,
        ]);
    }
}
