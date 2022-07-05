<?php

namespace App\Form;

use App\Entity\OnlineLoan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Positive;

class OnlineLoanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $loanEligibility = $options['loanEligibility'];
        $eligibleFdList = $options['eligibleFdList'];
        $fdChoicesList = [];
        $attrList = [];
        foreach ($eligibleFdList as $id=>$value) {
            $fdChoicesList[$id] = $id;
            $attrList[$id] = [
                'data-amount' => $value
            ];
        }
        $loanPlans = $options['loanPlans'];
        $loanPlansChoiceList = [];
        foreach ($loanPlans as $idx=>$loanPlan) {
            $loanPlansChoiceList[$loanPlan['Interest_Rate'] . '%, ' . $loanPlan['Duration'] . ' months'] = $loanPlan['ID'];
        }

        if ($loanEligibility) {
            $builder
                ->add('fdId', ChoiceType::class, [
                    'choices' => $fdChoicesList,
                    'choice_attr' => $attrList
                ])
                ->add('loanType', ChoiceType::class, [
                    'choices' => [
                        'Personal' => 'PERSONAL',
                        'Business' => 'BUSINESS'
                    ]
                ])
                ->add('amount', MoneyType::class, [
                    'currency' => 'LKR',
                    'constraints' => [
                        new Positive(),
                        new GreaterThanOrEqual(1000)
                    ]
                ])
                ->add('planId', ChoiceType::class, [
                    'label' => 'Plan',
                    'choices' => $loanPlansChoiceList
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
            'eligibleFdList' => [],
            'loanPlans' => [],
        ]);

        $resolver->setAllowedTypes('loanEligibility', 'bool');
        $resolver->setAllowedTypes('eligibleFdList', 'array');
        $resolver->addAllowedTypes('loanPlans', 'array');
    }
}
