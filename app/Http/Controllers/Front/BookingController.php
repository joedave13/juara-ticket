<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\Booking\StoreBookingRequest;
use App\Models\Ticket;
use App\Services\BookingService;
use App\Services\TicketService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected TicketService $ticketService,
        protected BookingService $bookingService
    ) {
        //
    }

    public function create(Ticket $ticket): View
    {
        $data = $this->ticketService->getTicketDetail($ticket);

        return view('pages.booking.create', [
            'ticket' => $data
        ]);
    }

    public function store(StoreBookingRequest $request, Ticket $ticket): RedirectResponse
    {
        $validatedData = $request->validated();
        $amount = $this->bookingService->calculateAmount($ticket, $validatedData['total_participant']);

        $this->bookingService->storeBookingSession($ticket, $validatedData, $amount);

        return redirect('booking.payment');
    }
}
