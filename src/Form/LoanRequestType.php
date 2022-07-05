<?php

namespace App\Form;

use App\Entity\NormalLoan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Positive;

class LoanRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $loanPlans = $options['loanPlans'];
        $choiceList = [];
        foreach ($loanPlans as $idx=>$loanPlan) {
            $choiceList[$loanPlan['Interest_Rate'] . '%, ' . $loanPlan['Duration'] . ' months'] = $loanPlan['ID'];
        }


        $builder
            ->add('username', TextType::class, [
                'mapped' => false,
            ])
            ->add('accountNumber', TextType::class)
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
                'choices' => $choiceList
            ])
            ->add('reason', TextareaType::class, [
                'attr' => ['maxlength' => '500'],
                'required' => true
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NormalLoan::class,
            'loanPlans' => []
        ]);

        $resolver->addAllowedTypes('loanPlans', 'array');
    }
}
