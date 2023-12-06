<?php

namespace App\Observers;

use App\Http\Controllers\ExpirationMedicineController;
use App\Models\ExpirationMedicine;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class OrderItemObserver
{
    /**
     * Handle the Order "created" event.
     * @throws \Exception
     */
    public function created(OrderItem $item)
    {
        // when i create an order i should remove the purchased medicines
        // from the warehouse
        Log::debug('order items :' ,['amount' => $item]);
        $batches = ExpirationMedicine::query()
            ->where('medicine_id' , $item['medicine_id']) -> orderBy('expiration_date')->get();
        Log::debug('batches : ' , ['batch' => $batches]);
        foreach($batches as $batch){
            if($item['amount'] == 0) break;
            if($batch['amount'] > $item['amount']){
                $batch['amount'] -= $item['amount'];
                $batch->save();
                break;
            }else{
                $item['amount'] -= $batch->amount;
                $batch->delete();
            }

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
