<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Service\CommissionCalculator;

use Brick\Math\BigDecimal;
use Paywa\CommissionTask\Entity\UserOperation;

interface CommissionCalculatorInterface
{
    public function calculateDepositCommission(UserOperation $userOperation): BigDecimal;

    public function calculateWithdrawCommission(UserOperation $userOperation): BigDecimal;
}
