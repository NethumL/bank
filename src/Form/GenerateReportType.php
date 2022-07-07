<?php

namespace App\Form;

use App\Validator\BranchIDExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenerateReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $branches = $options['branches'];
        $branchChoiceList = [];
        foreach ($branches as $branch) {
            $branchChoiceList[$branch['Name']] = $branch['ID'];
        }


        $builder
            ->add('branch', ChoiceType::class, [
                'choices' => $branchChoiceList
            ])
            ->add('reportType', ChoiceType::class, [
                'choices' => [
                    'Total transaction report' => 'TOTAL_TRANSACTION_REPORT',
                    'Late loan instalments report' => 'LATE_LOAN_INSTALMENTS_REPORT'
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('generate', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'branches' => [],
        ]);
    }
}
