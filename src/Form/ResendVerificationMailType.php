<?php

namespace App\Form;

use App\Validator\EmailResendVerify;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class ResendVerificationMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'invalid_message' => 'The password fields must match.',
                'label' => "Email address",
                'required' => true,
                'constraints' => [new EmailResendVerify()]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'form-width-limit form-margin-top'],
        ]);
    }
}
