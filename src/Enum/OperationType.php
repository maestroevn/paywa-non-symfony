<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Enum;

enum OperationType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
}
