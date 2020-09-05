<?php

namespace App\Listeners;

use App\Events\PaymentEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PaymentEvent  $event
     * @return void
     */
    public function handle(PaymentEvent $event)
    {
        //
    }
}
