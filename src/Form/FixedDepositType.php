<?php

namespace App\Form;

use App\Repository\AccountRepository;
use App\Repository\FdPlanRepository;
use App\Validator\HasSufficientFundsForFd;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Positive;

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

        $savingsAccounts = $this->accountRepository->findByUser($options['userId'], "SAVINGS");
        $savingsAccountChoices = [];
        $savingsAccountAttrs = [];
        foreach ($savingsAccounts as $savingsAccount) {
            $savingsAccountChoices[$savingsAccount['Account_Number']] = $savingsAccount['Account_Number'];
            $savingsAccountAttrs[$savingsAccount['Account_Number']] = ['data-amount' => $savingsAccount['Amount']];
        }

        $builder
            ->add('savingsAccount', ChoiceType::class, ['choices' => $savingsAccountChoices, 'choice_attr' => $savingsAccountAttrs])
            ->add('plan', ChoiceType::class, ['choices' => $planChoices])
            ->add('amount', MoneyType::class, [
                'currency' => '',
                'constraints' => new Positive(message: "The amount must be positive"),
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
            'constraints' => [new HasSufficientFundsForFd()]
        ]);

        $resolver->addAllowedTypes('userId', 'string');
    }
}
