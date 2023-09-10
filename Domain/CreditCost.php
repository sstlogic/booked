<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once (ROOT_DIR . 'Domain/Values/Currency.php');
use Booked\Currency;

class CreditCost
{
    /**
     * @var float
     */
    private $cost;
    /**
     * @var string
     */
    private $currency;

    /**
     * @param float $cost
     * @param string $currency
     */
    public function __construct($cost = 0.0, $currency = 'USD')
    {
        $this->cost = $cost;
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    public function Cost()
    {
        return $this->cost;
    }

    /**
     * @return string
     */
    public function Currency()
    {
        return $this->currency;
    }

    /**
     * @param float|null $amount
     * @return string
     */
    public function FormatCurrency($amount = null)
    {
        $toFormat = is_null($amount) ? $this->Cost() : $amount;
        $currency = new Currency($this->Currency());
        return $currency->Format($toFormat);
    }

    /**
     * @param float $quantity
     * @return float
     */
    public function GetTotal($quantity)
    {
        return $this->Cost() * $quantity;
    }

    /**
     * @param float $quantity
     * @return string
     */
    public function GetFormattedTotal($quantity)
    {
        $total = $this->GetTotal($quantity);
        return $this->FormatCurrency($total);
    }
}