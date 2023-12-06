<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $all)
 */
class ExpirationMedicine extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
