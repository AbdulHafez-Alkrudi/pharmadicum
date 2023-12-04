<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpirationMedicine extends Model
{
    use HasFactory;

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
