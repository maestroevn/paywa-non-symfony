<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Service\CommissionCalculator;

use Exception;
use Brick\Math\BigDecimal;
use GuzzleHttp\Exception\GuzzleException;
use Paywa\CommissionTask\UserOperation;
use Paywa\CommissionTask\Service\ExchangeRates;
use Paywa\CommissionTask\Repository\UserOperationRepository;

class PrivateUserCommissionCalculator extends CommissionCalculator
{
    private const WITHDRAW_COMMISSION_PERCENT = 0.3;
    private const WITHDRAW_WEEKLY_LIMIT_ON_AMOUNT = 1000.00;
    private const WITHDRAW_WEEKLY_LIMIT_ON_COUNT = 3;

    /**
     * @param UserOperation $userOperation
     * @return BigDecimal
     * @throws GuzzleException
     * @throws Exception
     */
    public function calculateWithdrawCommission(UserOperation $userOperation): BigDecimal
    {
        /** @var UserOperationRepository $userOperationRepository */
        $userOperationRepository = UserOperationRepository::getInstance();
        $weeklyOperationsCount = $userOperationRepository->getUserWeeklyWithdrawOperationsCount(
            $userOperation->getOperationDate(),
            $userOperation->getUserId(),
        );

        $weeklyOperationsAmountTotal = $userOperationRepository->getUserWeeklyWithdrawOperationsAmountTotal(
            $userOperation->getOperationDate(),
            $userOperation->getUserId(),
        );

        if ($weeklyOperationsCount > self::WITHDRAW_WEEKLY_LIMIT_ON_COUNT) {
            return $this->calculateCommissionAmount(
                $userOperation->getAmount(),
                $userOperation->getCurrency(),
                self::WITHDRAW_COMMISSION_PERCENT,
            );
        } elseif ($weeklyOperationsAmountTotal->isLessThanOrEqualTo(self::WITHDRAW_WEEKLY_LIMIT_ON_AMOUNT)) {
            return $this->calculateCommissionAmount(
                BigDecimal::of(0),
                $userOperation->getCurrency(),
                self::WITHDRAW_COMMISSION_PERCENT,
            );
        } elseif (
            $weeklyOperationsAmountTotal
                ->minus($userOperation->getAmountInBaseCurrency())
                ->isGreaterThanOrEqualTo(self::WITHDRAW_WEEKLY_LIMIT_ON_AMOUNT)
        ) {
            return $this->calculateCommissionAmount(
                $userOperation->getAmount(),
                $userOperation->getCurrency(),
                self::WITHDRAW_COMMISSION_PERCENT,
            );
        } else {
            return $this->calculateCommissionAmount(
                ExchangeRates::getInstance()->convertFromEuro(
                    $weeklyOperationsAmountTotal->minus(self::WITHDRAW_WEEKLY_LIMIT_ON_AMOUNT),
                    $userOperation->getCurrency(),
                ),
                $userOperation->getCurrency(),
                self::WITHDRAW_COMMISSION_PERCENT,
            );
        }
    }
}
