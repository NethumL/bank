<?php

namespace App\Form;

use App\Repository\AccountRepository;
use App\Repository\FdPlanRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Range;

class FixedDepositType extends AbstractType
{
    private AccountRepository $accountRepository;
    private FdPlanRepository $fdPlanRepository;

    public function __construct(AccountRepository $accountRepository, FdPlanRepository $fdPlanRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->fdPlanRepository = $fdPlanRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $plans = $this->fdPlanRepository->findAll();
        $planChoices = [];
        foreach ($plans as $plan) {
            $planChoices[$plan['Duration'] . " months, " . $plan['Interest_Rate'] . "%"] = $plan['ID'];
        }

        $savingsAccount = $this->accountRepository->findOne($options['savingsAccount']);
        $maxAmount = $savingsAccount['Amount'];

        $builder
            ->add('savingsAccount', TextType::class, ['attr' => ['readonly' => true]])
            ->add('plan', ChoiceType::class, ['choices' => $planChoices])
            ->add('amount', MoneyType::class, [
                'currency' => '',
                'constraints' => new Range([
                    'min' => 0, 'max' => floatval($maxAmount), 'notInRangeMessage' => 'Deposit must be between {{ min }} and {{ max }}'
                ]),
                'label' => 'Amount (limit: ' . $maxAmount . ')'
            ])
            ->add('agree', CheckboxType::class, [
                'label' => 'Agree to Terms and Conditions',
                'required' => true,
                'constraints' => new IsTrue(message: "Please check this box!")
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'userId' => null,
            'savingsAccount' => null,
        ]);

        $resolver->addAllowedTypes('userId', 'string');
        $resolver->addAllowedTypes('savingsAccount', 'string');
    }
}
