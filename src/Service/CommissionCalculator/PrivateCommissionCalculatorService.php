<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Service\CommissionCalculator;

use Exception;
use Brick\Math\BigDecimal;
use GuzzleHttp\Exception\GuzzleException;
use Paywa\CommissionTask\Entity\UserOperation;
use Paywa\CommissionTask\Service\ExchangeRateService;
use Paywa\CommissionTask\Repository\UserOperationRepository;

class PrivateCommissionCalculatorService extends CommissionCalculatorServiceBase
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
            $userOperation,
        );

        $weeklyOperationsAmountTotal = $userOperationRepository->getUserWeeklyWithdrawOperationsAmountTotal(
            $userOperation,
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
                ExchangeRateService::getInstance()->convertFromEuro(
                    $weeklyOperationsAmountTotal->minus(self::WITHDRAW_WEEKLY_LIMIT_ON_AMOUNT),
                    $userOperation->getCurrency(),
                ),
                $userOperation->getCurrency(),
                self::WITHDRAW_COMMISSION_PERCENT,
            );
        }
    }
}
