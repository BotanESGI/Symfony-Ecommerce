<?php

declare(strict_types=1);

namespace App\Enum;

enum ReviewStatusEnum: string
{
    case PENDING = 'PENDING';
    case VALIDATED = 'VALIDATED';
    case REJECTED = 'REJECTED';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'PENDING',
            self::VALIDATED => 'VALIDATED',
            self::REJECTED => 'REJECTED',
        };
    }
}
