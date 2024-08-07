<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;

class BookingRepository implements BookingRepositoryInterface
{
    public function create(array $data): Booking
    {
        return Booking::query()->create($data);
    }

    public function getBookingDetail(string $code, string $phone): Booking
    {
        return Booking::with(['ticket', 'ticket:city'])
            ->where('code', $code)
            ->where('phone', $phone)
            ->first();
    }
}
