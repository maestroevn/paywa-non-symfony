<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Repository;

use Exception;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Paywa\CommissionTask\Enum\OperationType;
use Paywa\CommissionTask\Entity\UserOperation;
use Paywa\CommissionTask\CommonCore\Singleton;

class UserOperationRepository extends Singleton
{
    /** @var UserOperation[][][] */
    private array $operations = [];

    /**
     * @param UserOperation $operation
     * @return UserOperationRepository
     * @throws Exception
     */
    public function addOperation(UserOperation $operation): UserOperationRepository
    {
        $operationWeekUniqueId = $operation->getOperationWeekUniqueId();

        if (false === array_key_exists($operation->getUserId(), $this->operations)) {
            $this->operations[$operation->getUserId()] = [];
        }

        if (false === array_key_exists($operationWeekUniqueId, $this->operations[$operation->getUserId()])) {
            $this->operations[$operation->getUserId()][$operationWeekUniqueId] = [];
        }

        $this->operations[$operation->getUserId()][$operationWeekUniqueId][] = $operation;

        return $this;
    }

    /**
     * @param UserOperation $nexOperation
     * @return int
     */
    public function getUserWeeklyWithdrawOperationsCount(UserOperation $nexOperation): int
    {
        $count = 0;
        foreach (
            $this->operations
                [$nexOperation->getUserId()]
                [$nexOperation->getOperationWeekUniqueId()] as $operation
        ) {
            if ($operation->getOperationType() === OperationType::WITHDRAW) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param UserOperation $nexOperation
     * @return BigDecimal
     * @throws MathException
     */
    public function getUserWeeklyWithdrawOperationsAmountTotal(UserOperation $nexOperation): BigDecimal
    {
        $weeklyTotal = BigDecimal::of(0);

        foreach (
            $this->operations
            [$nexOperation->getUserId()]
            [$nexOperation->getOperationWeekUniqueId()] as $operation
        ) {
            if ($operation->getOperationType() !== OperationType::WITHDRAW) {
                continue;
            }
            $weeklyTotal = $weeklyTotal->plus($operation->getAmountInBaseCurrency());
        }

        return $weeklyTotal;
    }
}
