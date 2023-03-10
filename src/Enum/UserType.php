<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Enum;

enum UserType: string
{
    case PRIVATE = 'private';
    case BUSINESS = 'business';
}
