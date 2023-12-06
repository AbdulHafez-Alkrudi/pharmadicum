<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteMedicine extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];
    /*public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }*/
}
