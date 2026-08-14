<?php

namespace App\Enum\Order;

use App\Trait\Core\EnumLabelTrait;

enum OrderMovementType: string
{
    use EnumLabelTrait;

    case IN = 'in';
    case OUT = 'out';

    public function getLabel(): string
    {
        return match ($this) {
            self::IN => 'movementTypeIn',
            self::OUT => 'movementTypeOut',
        };
    }

    private function getDomain(): ?string
    {
        return 'order';
    }
}
