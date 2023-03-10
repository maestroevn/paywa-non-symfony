<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Service\CommissionCalculator;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\RoundingNecessaryException;
use Paywa\CommissionTask\UserOperation;

class BusinessUserCommissionCalculator extends CommissionCalculator
{
    private const WITHDRAW_COMMISSION_PERCENT = 0.5;

    /**
     * @param UserOperation $userOperation
     * @return BigDecimal
     * @throws MathException
     * @throws RoundingNecessaryException
     */
    public function calculateWithdrawCommission(UserOperation $userOperation): BigDecimal
    {
        return $this->calculateCommissionAmount(
            $userOperation->getAmount(),
            $userOperation->getCurrency(),
            self::WITHDRAW_COMMISSION_PERCENT,
        );
    }
}
