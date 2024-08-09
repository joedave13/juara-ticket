<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookingStatus: string implements HasLabel
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case CANCEL = 'cancel';

    public function getLabel(): ?string
    {
        return str(str($this->value)->replace('_', ''))->title();
    }

    public function getColorLabelFront(): string
    {
        return match ($this) {
            self::PENDING => 'bg-[#13181D]',
            self::SUCCESS => 'bg-[#07B704]',
            self::CANCEL => 'bg-[#D40000]'
        };
    }
}
