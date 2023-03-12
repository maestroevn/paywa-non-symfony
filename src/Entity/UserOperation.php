<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Entity;

use DateTime;
use Exception;
use Brick\Math\BigDecimal;
use GuzzleHttp\Exception\GuzzleException;
use Paywa\CommissionTask\Enum\Currency;
use Paywa\CommissionTask\Enum\UserType;
use Paywa\CommissionTask\Enum\OperationType;
use Paywa\CommissionTask\Helper\DateHelper;
use Paywa\CommissionTask\Service\ExchangeRateService;

class UserOperation
{
    private const BASE_CURRENCY = Currency::EUR;

    protected DateTime $operationDate;
    protected int $userId;
    protected UserType $userType;
    protected OperationType $operationType;
    protected BigDecimal $amount;
    protected Currency $currency;

    protected string $operationWeekUniqueId;
    protected BigDecimal $amountInBaseCurrency;

    /**
     * @param DateTime $date
     * @return string
     */
    private function composeWeekUniqueId(DateTime $date): string
    {
        // To create a unique `weekId` of any week let's take Monday of the same week of given date,
        // take year, take week number and concat

        $monday = DateHelper::getSameWeekMonday($date);

        $operationYear = $monday->format('Y');
        $operationWeekNumberInYear = $monday->format('W');

        return sprintf(
            '%s_%s',
            $operationYear,
            $operationWeekNumberInYear,
        );
    }

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

        $this->setOperationWeekUniqueId($this->composeWeekUniqueId($this->getOperationDate()));

        if ($this->getCurrency() === self::BASE_CURRENCY) {
            $this->setAmountInBaseCurrency($this->getAmount());
        } else {
            $this->setAmountInBaseCurrency(
                ExchangeRateService::getInstance()->convertToEuro($this->getAmount(), $this->getCurrency())
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
    protected function setOperationDate(DateTime $operationDate): void
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
    protected function setUserId(int $userId): void
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
    protected function setUserType(UserType $userType): void
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
    protected function setOperationType(OperationType $operationType): void
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
    protected function setAmount(BigDecimal $amount): void
    {
        $this->amount = $amount;
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
    protected function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getOperationWeekUniqueId(): string
    {
        return $this->operationWeekUniqueId;
    }

    /**
     * @param string $operationWeekUniqueId
     */
    protected function setOperationWeekUniqueId(string $operationWeekUniqueId): void
    {
        $this->operationWeekUniqueId = $operationWeekUniqueId;
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
    protected function setAmountInBaseCurrency(BigDecimal $amountInBaseCurrency): void
    {
        $this->amountInBaseCurrency = $amountInBaseCurrency;
    }
}
