<?php

namespace App\Util;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;
use Money\Parser\DecimalMoneyParser;

class MoneyUtils
{
    const CURRENCY_CODE = "LKR";
    private MoneyParser $parser;
    private MoneyFormatter $formatter;
    private Currency $currency;

    public function __construct()
    {
        $currencies = new ISOCurrencies();
        $this->parser = new DecimalMoneyParser($currencies);
        $this->formatter = new DecimalMoneyFormatter($currencies);
        $this->currency = new Currency(self::CURRENCY_CODE);
    }

    public function parseString(string $amount): Money
    {
        return $this->parser->parse($amount, $this->currency);
    }

    public function format(Money $amount): string
    {
        return $this->formatter->format($amount);
    }
}
