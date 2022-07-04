<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstalmentPaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $accountChoices = [];
        foreach ($options['accounts'] as $account) {
            $accountChoices[$account['Account_Number'] . ' (Rs. ' . $account['Amount'] . ')'] = $account['Account_Number'];
        }

        $builder
            ->add('from', ChoiceType::class, ['choices' => $accountChoices])
            ->add('amount', TextType::class, ['disabled' => true])
            ->add('pay', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'accounts' => []
        ]);

        $resolver->setAllowedTypes('accounts', 'array');
    }
}
