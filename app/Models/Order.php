<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static find($id)
 */
class Order extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];
    //protected $with = ['order_items'];
    public function user() :belongsTo{
        return $this->belongsTo(User::class );
    }

    /*public function items(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class , 'order_items')->withPivot(['order_id','medicine_id','amount','unit_price']);
    }*/
    public function items(){
        return $this->hasMany(OrderItem::class);
    }
    public function order_status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function payment_status(): BelongsTo
    {
        return $this->belongsTo(PaymentStatus::class);
    }
}
