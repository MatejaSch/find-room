<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('pricePerNight')
            ->add('description')
            ->add('singleBed', null, [
                'attr' => [
                    'class' => 'bed-quantity',
                    'min' => 0,
                    'max' => 10,
                ]
            ])
            ->add('doubleBed', null, [
                'attr' => [
                    'class' => 'bed-quantity',
                    'min' => 0,
                    'max' => 10,
                ]
            ])
            ->add('images', FileType::class, [
                'label' => 'Offer images',
                'multiple' => true,

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new All([
                        'constraints' => new File([
                            'maxSize' => '1M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Allowed image types are JPEG, JPG and PNG.',
                        ])
                    ])
                ],
            ])
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
