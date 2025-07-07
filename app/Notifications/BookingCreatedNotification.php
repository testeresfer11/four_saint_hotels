<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
       return [
           'type'       => "booking_created",
           'title'      => "Booking created successfully",
           'description'=> "Your booking created  successfully",
           'route'      => route('admin.user.list')
        ];
    }
}
