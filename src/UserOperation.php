<?php

declare(strict_types=1);

namespace Paywa\CommissionTask;

use DateTime;
use Exception;
use Brick\Math\BigDecimal;
use GuzzleHttp\Exception\GuzzleException;
use Paywa\CommissionTask\Enum\Currency;
use Paywa\CommissionTask\Enum\UserType;
use Paywa\CommissionTask\Enum\OperationType;
use Paywa\CommissionTask\Service\ExchangeRates;

class UserOperation
{
    private const BASE_CURRENCY = Currency::EUR;

    protected DateTime $operationDate;
    protected int $userId;
    protected UserType $userType;
    protected OperationType $operationType;
    protected BigDecimal $amount;
    protected BigDecimal $amountInBaseCurrency;
    protected Currency $currency;

    /**
     * @throws GuzzleException
     * @throws Exception
     * @psalm-param array<string> $data
     */
    public function __construct(array $data)
    {
        $this->setOperationDate(new DateTime($data[0]));
        $this->setUserId(intval($data[1]));
        $this->setUserType(UserType::from($data[2]));
        $this->setOperationType(OperationType::from($data[3]));
        $this->setAmount(BigDecimal::of($data[4]));
        $this->setCurrency(Currency::from($data[5]));

        if ($this->getCurrency() === self::BASE_CURRENCY) {
            $this->setAmountInBaseCurrency($this->getAmount());
        } else {
            $this->setAmountInBaseCurrency(
                ExchangeRates::getInstance()->convertToEuro($this->getAmount(), $this->getCurrency())
            );
        }
    }

    /**
     * @return DateTime
     */
    public function getOperationDate(): DateTime
    {
        return $this->operationDate;
    }

    /**
     * @param DateTime $operationDate
     */
    public function setOperationDate(DateTime $operationDate): void
    {
        $this->operationDate = $operationDate;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return UserType
     */
    public function getUserType(): UserType
    {
        return $this->userType;
    }

    /**
     * @param UserType $userType
     */
    public function setUserType(UserType $userType): void
    {
        $this->userType = $userType;
    }

    /**
     * @return OperationType
     */
    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    /**
     * @param OperationType $operationType
     */
    public function setOperationType(OperationType $operationType): void
    {
        $this->operationType = $operationType;
    }

    /**
     * @return BigDecimal
     */
    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    /**
     * @param BigDecimal $amount
     */
    public function setAmount(BigDecimal $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return BigDecimal
     */
    public function getAmountInBaseCurrency(): BigDecimal
    {
        return $this->amountInBaseCurrency;
    }

    /**
     * @param BigDecimal $amountInBaseCurrency
     */
    public function setAmountInBaseCurrency(BigDecimal $amountInBaseCurrency): void
    {
        $this->amountInBaseCurrency = $amountInBaseCurrency;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }
}
