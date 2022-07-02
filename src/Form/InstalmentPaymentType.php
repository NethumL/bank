<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class InstalmentPaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('creditCardNumber', TextType::class)
            ->add('expiryMonth', IntegerType::class, ['constraints' => [new Assert\Range(['min' => 1, 'max' => 12])], 'attr' => ['min' => 1, 'max' => 12]])
            ->add('expiryYear', IntegerType::class, ['constraints' => [new Assert\Range(['min' => (int)date("y")])], 'attr' => ['min' => (int)date("y")]])
            ->add('cvv', NumberType::class, ['input' => 'string'])
            ->add('pay', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
