<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderStatusEnum extends Enum
{
    const Failed = 0;
    const Active = 1;
    const Inactive = 2;
}
