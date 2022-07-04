<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;

class PasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'constraints' => [new UserPassword(message: 'The password is incorrect.')]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords must match',
                'first_options' => ['label' => 'New password'],
                'second_options' => ['label' => 'Confirm new password'],
                'constraints' => [new Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters')]
            ])
            ->add('submit', SubmitType::class)
        ;
    }
}
