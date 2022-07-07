<?php

namespace App\Controller;

use App\Form\GenerateReportType;
use App\Repository\BranchRepository;
use App\Repository\TransactionRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    #[Route('/report/generate', name: 'app_report_generate')]
    public function generate(
        Request $request,
        BranchRepository $branchRepository
    ): Response
    {
        $report = [];
        $branches = $branchRepository->findAll();

        $form = $this->createForm(
            GenerateReportType::class,
            $report,
            [
                'branches' => $branches
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $branchID = $formData['branch'];
            $reportType = $formData['reportType'];
            /* @var DateTime $date */
            $date = $formData['date'];

            return $this->redirectToRoute('app_report', [
                'branch' => $branchID,
                'reportType' => $reportType,
                'date' => $date->format('Y-m-d')
            ]);
        }

        return $this->renderForm('report/generate.html.twig', [
            'form' => $form,
            'controller_name' => 'LoanController',
        ]);
    }

    #[Route('/report/', name: 'app_report')]
    public function report(
        Request $request,
        BranchRepository $branchRepository,
        TransactionRepository $transactionRepository
    ): Response
    {
        if (
            !$request->query->has('branch') ||
            !$request->query->has('reportType') ||
            !$request->query->has('date')
        ) {
            return $this->redirectToRoute('app_generate_report');
        }

        $branchID = $request->query->get('branch');
        $branch = $branchRepository->findOneById($branchID);
        $date = $request->query->get('date');
        $reportType = $request->query->get('reportType');
        if (!$branch) {
            return $this->redirectToRoute('app_generate_report');
        }

        if ($reportType==='TOTAL_TRANSACTION_REPORT') {
            // Get all the transactions related to branch ID
            $transactions = $transactionRepository->findByBranchIDAndDate($branchID, $date);

            return $this->render('report/transaction.html.twig', [
                'branch' => $branch['Name'],
                'date' => $date,
                'transactions' => $transactions
            ]);
        } else if ($reportType==='LATE_LOAN_INSTALMENTS_REPORT') {

        }
        else {
            return $this->redirectToRoute('app_generate_report');
        }
    }
}
