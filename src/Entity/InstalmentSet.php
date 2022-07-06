<?php

namespace App\Entity;

use App\Util\MoneyUtils;
use Symfony\Component\Validator\Constraints\Date;

class InstalmentSet
{
    private string $loanID;
    private array $instalments;
    private string $amount;
    private string $interest;
    private int $duration;
    private MoneyUtils $moneyUtils;

    public function __construct(string $loanID, string $amount, string $interest, int $duration)
    {
        $this->loanID = $loanID;
        $this->amount = $amount;
        $this->interest = $interest;
        $this->duration = $duration;
        $this->moneyUtils = new MoneyUtils();


        $moneyAmount = $this->moneyUtils->parseString($amount);

        $moneyTotalInterest = $moneyAmount
            ->multiply($interest)
            ->multiply($duration)
            ->divide(1200);

        $moneyTotalAmountToPay = $moneyAmount->add($moneyTotalInterest);
        $monthlyInstalments = $moneyTotalAmountToPay->allocateTo($duration);

        $currentDate = new \DateTime();
        for ($i=1; $i <= $duration; $i++) {
            $instalment = [
                'year' => $currentDate->format('Y'),
                'month'=> $currentDate->format('m'),
                'amount'=> $this->moneyUtils->format($monthlyInstalments[$i-1])
            ];
            $this->instalments[] = $instalment;
            $oneMonthInterval = new \DateInterval('P1M');
            $currentDate->add($oneMonthInterval);
        }
    }

    public function getInstalments()
    {
        return $this->instalments;
    }

    public function getInterest()
    {
        return $this->interest;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getLoanID()
    {
        return $this->loanID;
    }

}
