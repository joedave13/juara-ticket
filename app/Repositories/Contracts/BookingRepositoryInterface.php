<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;

interface BookingRepositoryInterface
{
    public function create(array $data): Booking;

    public function getBookingDetail(string $code, string $phone): Booking;
}
