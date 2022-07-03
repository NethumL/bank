<?php

namespace App\Form;

use App\Entity\Employee;
use App\Repository\BranchRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class NewEmployeeType extends AbstractType
{
    private BranchRepository $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userTypes = [];
        foreach ($options['userTypeChoices'] as $userType) {
            $userTypes[$userType] = $userType;
        }

        $branches = $this->branchRepository->findAll();
        $branchChoices = [];
        foreach ($branches as $branch) {
            $branchChoices[$branch['Name']] = $branch['ID'];
        }

        $builder
            ->add('username', TextType::class)
            ->add('name', TextType::class)
            ->add('password', PasswordType::class, [
                'constraints' => [new Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters')]
            ])
            ->add('userType', ChoiceType::class, ['choices' => $userTypes])
            ->add('phoneNumber', TelType::class)
            ->add('dob', BirthdayType::class, ['widget' => 'single_text'])
            ->add('address', TextareaType::class)
            ->add('branchId', ChoiceType::class, ['choices' => $branchChoices])
            ->add('add', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
            'userTypeChoices' => ['EMPLOYEE'],
        ]);

        $resolver->setAllowedTypes('userTypeChoices', 'array');
    }
}
