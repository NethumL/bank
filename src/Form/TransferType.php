<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Repository\AccountRepository;
use App\Validator\AccountExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class TransferType extends AbstractType
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $accounts = $this->accountRepository->findByUser($options['userId']);
        $accountChoices = [];
        foreach ($accounts as $account) {
            $accountChoices[$account['Account_Number'] . ": " . $account['Account_Type']] = $account['Account_Number'];
        }

        $builder
            ->add('from', ChoiceType::class, ['choices' => $accountChoices])
            ->add('to', TextType::class, ['constraints' => [new AccountExists()]])
            ->add('type', HiddenType::class)
            ->add('amount', MoneyType::class, ['currency' => '', 'constraints' => [new Positive()]])
            ->add('description', TextType::class, ['required' => false])
            ->add('transfer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'userId' => null,
        ]);

        $resolver->addAllowedTypes('userId', 'string');
    }
}
