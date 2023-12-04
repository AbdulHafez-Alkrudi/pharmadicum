<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, $id)
 */
class Medicine extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];
    /*protected $withCount = [
        'favored as popularity'
    ];*/
    public function batches(): HasMany
    {
        return $this->hasMany(ExpirationMedicine::class );
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class );
    }
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }


    /*public function favored(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'favorite_medicines');
    }*/
    public function favorite_users(): HasMany
    {
        return $this->hasMany(FavoriteMedicine::class);
    }


    // Here I'm creating the filtering function that will receive the category ID or the name of
    // the medicine that the customer is searching for
    //
    // the first argument is the query builder which laravel pass it by default
    // and the second argument is the array that contains the category name or the medicine name or
    // both of them
    public function scopeFilter($query , array $filters)
    {

        // here "when" function just checks if there is a category key in the filter array
        // I'm searching about the medicines that have a specific category
        // the search could be done using the category id
       $query->when($filters['category'] ?? false , fn($query , $category) =>
            $query->whereHas('category' , fn($query) =>
                    $query->where('id' , $category)
            )
       );
       // here I'll check if the user is searching about a specific medicine by sending
       // its name :

        $query->when($filters['search'] ?? false , fn($query , $search) =>
                $query -> where  ('scientific_name_EN' , 'REGEXP' , $search)
                       -> orWhere('scientific_name_AR' , 'REGEXP' , $search)
                       -> orWhere('economic_name_EN'   , 'REGEXP' , $search)
                       -> orWhere('economic_name_AR'   , 'REGEXP' , $search)
        );


    }
}
