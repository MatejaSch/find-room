<?php

namespace App\Form;

use App\Validator\CheckInDate;
use App\Validator\CheckOutDate;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class FilterOffersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('capacity', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 99,
                ],
                'required' => false,
                'constraints' => [
                    new Positive(),
                    new Length(['max' => 2]),
                ],

            ])
            ->add('checkIn', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Check in',
                'required' => false,
                'attr' => [
                    'min' => $options['checkInMinimum'],
                    'max' => $options['checkInMaximum'],
                ],
                'constraints' => [new CheckInDate()]
            ])
            ->add('checkOut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Check out',
                'required' => false,
                'attr' => [
                    'min' => $options['checkOutMinimum'],
                    'max' => $options['checkOutMaximum'],
                ],
                'constraints' => [new CheckOutDate()]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'form-filter'],
            'checkInMinimum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 day")) : date("Y-m-d"),
            'checkInMaximum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 year -1 day")) : date("Y-m-d", strtotime("+1 year -2 day")),
            'checkOutMinimum' => date("H") > 3 ? date("Y-m-d", strtotime("+2 day")) : date("Y-m-d", strtotime("+1 day")),
            'checkOutMaximum' => date("H") > 3 ? date("Y-m-d", strtotime("+1 year")) : date("Y-m-d", strtotime("+1 year -1 day")),
        ]);
    }
}
