<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserve
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // when i create an order i should remove the purchased medicines
        // from the warehouse

        $order_items = $order->items ;

        foreach($order_items as $item){
            // here foreach item i should update its quantity in the
            // stock
            // to retrieve the data for each item what should I do??
            // i'll call a function that gives me the hole information about
            // the medicine
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
