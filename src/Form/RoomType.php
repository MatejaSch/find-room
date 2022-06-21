<?php

namespace App\Form;

use App\Entity\Bed;
use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

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
//            ->add('images', FileType::class, [
//                'label' => 'Room images',
//                'multiple' => true,
//
//                // unmapped means that this field is not associated to any entity property
//                'mapped' => false,
//
//                // make it optional so you don't have to re-upload the PDF file
//                // every time you edit the Product details
//                'required' => false,
//
//                // unmapped fields can't define their validation using annotations
//                // in the associated entity, so you can use the PHP constraint classes
//                'constraints' => [
//                    new All([
//                        'constraints' => new File([
//                            'maxSize' => '1M',
//                            'mimeTypes' => [
//                                'image/jpeg',
//                                'image/png',
//                            ],
//                            'mimeTypesMessage' => 'Allowed image types are JPEG, JPG and PNG.',
//                        ])
//                    ])
//                ],
//            ])
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
