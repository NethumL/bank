<?php

namespace App\Form;

use App\Repository\SavingsPlanRepository;
use App\Validator\AgeSatisfiesSavingsPlan;
use App\Validator\BalanceSatisfiesSavingsPlan;
use App\Validator\UsernameExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class AccountType extends AbstractType
{
    private SavingsPlanRepository $savingsPlanRepository;

    public function __construct(SavingsPlanRepository $savingsPlanRepository)
    {
        $this->savingsPlanRepository = $savingsPlanRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $accountTypeChoices = [
            'Current' => 'CURRENT',
            'Savings' => 'SAVINGS',
        ];

        $savingsPlan = $this->savingsPlanRepository->findAll();
        $savingsPlanChoices = [];
        foreach ($savingsPlan as $plan) {
            $minAge = strval($plan['Minimum_Age'] ?? 0);
            $maxAge = strval($plan['Maximum_Age'] ?? '');
            $ageRange = $minAge;
            if (empty($maxAge)) {
                $ageRange .= "+";
            } else {
                $ageRange .= "-" . $maxAge;
            }
            $label = $plan['Name']
                . " (age: " . $ageRange . ","
                . " rate: " . $plan['Interest_Rate'] . "%,"
                . " min: " . $plan['Minimum_Balance'] . ")";
            $savingsPlanChoices[$label] = $plan['ID'];
        }

        $builder
            ->add('username', TextType::class, ['constraints' => [new UsernameExists()]])
            ->add('accountType', ChoiceType::class, ['choices' => $accountTypeChoices])
            ->add('savingsPlan', ChoiceType::class, ['choices' => $savingsPlanChoices, 'required' => false])
            ->add('amount', MoneyType::class, [
                'currency' => 'LKR',
                'constraints' => [new PositiveOrZero()]
            ])
            ->add('create', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [new AgeSatisfiesSavingsPlan(), new BalanceSatisfiesSavingsPlan()],
        ]);
    }
}
