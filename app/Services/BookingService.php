<?php

namespace App\Services;

use App\Models\Ticket;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\TicketRepositoryInterface;
use Illuminate\Support\Facades\Session;

class BookingService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected TicketRepositoryInterface $ticketRepository,
        protected BookingRepositoryInterface $bookingRepository
    ) {
        //
    }

    public function calculateAmount(Ticket $ticket, $totalParticipant): array
    {
        $ticketPrice = $this->ticketRepository->getTicketPrice($ticket);

        $subTotal = $ticketPrice * $totalParticipant;
        $taxAmount = 0.11 * $subTotal;
        $totalAmount = $subTotal + $taxAmount;

        return [
            'sub_total' => $subTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ];
    }

    public function storeBookingSession(Ticket $ticket, array $data, $amount): void
    {
        Session::put('booking_session', [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'ticket_id' => $ticket->id,
            'total_participant' => $data['total_participant'],
            'booking_date' => $data['booking_date'],
            'price' => $ticket->price,
            'sub_total' => $amount['sub_total'],
            'tax_amount' => $amount['tax_amount'],
            'total' => $amount['total_amount']
        ]);
    }
}
