<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'display_order',
    ];

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to order categories by display_order and name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')
                     ->orderBy('name', 'asc');
    }

    /**
     * Get the count of active products for this category.
     *
     * @return int
     */
    public function activeProductsCount()
    {
        return $this->products()->where('status', 'active')->count();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('sidebar_categories');
        });
        
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('sidebar_categories');
        });
    }
}
