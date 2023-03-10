<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Repository;

use Exception;
use DateTime;
use DateInterval;
use Brick\Math\BigDecimal;
use Paywa\CommissionTask\UserOperation;
use Paywa\CommissionTask\Enum\OperationType;
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
        $operationWeekUniqueId = $this->composeWeekUniqueId($operation->getOperationDate());

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
     * @param DateTime $operationDate
     * @param int $userId
     * @return int
     * @throws Exception
     */
    public function getUserWeeklyWithdrawOperationsCount(DateTime $operationDate, int $userId): int
    {
        $operationWeekUniqueId = $this->composeWeekUniqueId($operationDate);

        $count = 0;
        foreach ($this->operations[$userId][$operationWeekUniqueId] as $operation) {
            if ($operation->getOperationType() === OperationType::WITHDRAW) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param DateTime $operationDate
     * @param int $userId
     * @return BigDecimal
     * @throws Exception
     */
    public function getUserWeeklyWithdrawOperationsAmountTotal(DateTime $operationDate, int $userId): BigDecimal
    {
        $operationWeekUniqueId = $this->composeWeekUniqueId($operationDate);

        $weeklyTotal = BigDecimal::of(0);
        foreach ($this->operations[$userId][$operationWeekUniqueId] as $operation) {
            if ($operation->getOperationType() !== OperationType::WITHDRAW) {
                continue;
            }
            $weeklyTotal = $weeklyTotal->plus($operation->getAmountInBaseCurrency());
        }

        return $weeklyTotal;
    }

    /**
     * @param DateTime $date
     * @return string
     */
    private function composeWeekUniqueId(DateTime $date): string
    {
        // To create a unique `weekId` of any week let's take Monday of the same week of given date,
        // take year, take week number and concat

        $monday = $this->getSameWeekMonday($date);

        $operationYear = $monday->format('Y');
        $operationWeekNumberInYear = $monday->format('W');

        return $operationYear . '_' . $operationWeekNumberInYear;
    }

    /**
     * @param DateTime $date
     * @return DateTime
     */
    private function getSameWeekMonday(DateTime $date): DateTime
    {
        $dayOfWeek = (int)$date->format('w');
        if ($dayOfWeek >= 1) {
            $intervalString = ($dayOfWeek - 1) . ' days';
        } else {
            $intervalString = '5 days';
        }
        $interval = DateInterval::createFromDateString($intervalString);

        return $date->sub($interval);
    }
}
