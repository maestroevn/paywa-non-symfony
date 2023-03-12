<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Service\CommissionCalculator;

use Exception;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\RoundingNecessaryException;
use Paywa\CommissionTask\Enum\Currency;
use Paywa\CommissionTask\Enum\OperationType;
use Paywa\CommissionTask\Entity\UserOperation;

abstract class CommissionCalculatorServiceBase implements CommissionCalculatorInterface
{
    private const DEPOSIT_COMMISSION_PERCENT = 0.03;

    /**
     * Commission fees are rounded up to currency's decimal places.
     * For example, 0.023 EUR should be rounded up to 0.03 EUR
     *
     * @param BigDecimal $amount
     * @param Currency $currency
     * @param float $percent
     * @return BigDecimal
     * @throws RoundingNecessaryException
     * @throws MathException
     */
    protected function calculateCommissionAmount(BigDecimal $amount, Currency $currency, float $percent): BigDecimal
    {
        $currencyDecimalPlaces = $currency->decimalPlaces();

        return $amount
            ->multipliedBy($percent)
            ->dividedBy(100, $currencyDecimalPlaces, RoundingMode::UP);
    }

    /**
     * @param UserOperation $userOperation
     * @return BigDecimal
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function calculateDepositCommission(UserOperation $userOperation): BigDecimal
    {
        return $this->calculateCommissionAmount(
            $userOperation->getAmount(),
            $userOperation->getCurrency(),
            self::DEPOSIT_COMMISSION_PERCENT,
        );
    }

    /**
     * @param UserOperation $userOperation
     * @return BigDecimal
     * @throws Exception
     */
    public function calculateCommission(UserOperation $userOperation): BigDecimal
    {
        switch ($userOperation->getOperationType()) {
            case OperationType::DEPOSIT:
                return $this->calculateDepositCommission($userOperation);
            case OperationType::WITHDRAW:
                return $this->calculateWithdrawCommission($userOperation);
        }

        throw new Exception(
            sprintf(
                'Calculation for `%s` operation type cannot be handled.',
                $userOperation->getOperationType()->value,
            ),
        );
    }
}
