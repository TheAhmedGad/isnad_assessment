<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = ['name', 'current_stock_in_grams', 'max_capacity_in_grams', 'alert_sent_at'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('amount_in_grams');
    }

    public function stock(): HasMany
    {
        return $this->hasMany(IngredientStock::class);
    }

}
