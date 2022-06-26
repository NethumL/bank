<?php

namespace App\Form;

use App\Entity\OnlineLoan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OnlineLoanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $loanEligibility = $options['loanEligibility'];
        $eligibleFdList = $options['eligibleFdList'];
        $fdChoiceList = [];
        foreach ($eligibleFdList as $fd) {
            $fdChoiceList[$fd['Account_Number']] = $fd['Account_Number'];
        }
        if ($loanEligibility) {
            $builder
                ->add('fdId', ChoiceType::class, [
                    'choices' => $fdChoiceList,
                ])
                ->add('loanType', ChoiceType::class, [
                    'choices' => [
                        'Personal' => 'PERSONAL',
                        'Business' => 'BUSINESS'
                    ]
                ])
                ->add('amount', MoneyType::class, [
                    'currency' => 'LKR'
                ])
                ->add('submit', SubmitType::class)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OnlineLoan::class,
            'loanEligibility' => true,
            'eligibleFdList' => []
        ]);

        $resolver->setAllowedTypes('loanEligibility', 'bool');
        $resolver->setAllowedTypes('eligibleFdList', 'array');
    }
}
