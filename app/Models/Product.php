<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        "name", "slug", "stock", "featured", "price", "details", "description", "category_id", "brand_id"
    ];
    protected $with = ["photos", "category", "brand"];

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['search'] ?? false,
            fn ($query, $search) =>
            $query->where(
                fn ($query) =>
                $query
                    ->where('name', 'like', "%" . $search . "%")
                    ->orWhere('details', 'like', "%" . $search . "%")
            )
        );

        $query->when(
            $filters['category'] ?? false,
            fn ($query, $category) =>
            $query->whereHas(
                'category',
                fn ($query) =>
                $query->where('slug', $category)
            )
        );

        $query->when(
            $filters['sort'] ?? false,
            function ($query, $sort) {
                if ($sort == 'low_high') {
                    $query->orderBy('price');
                } elseif ($sort == 'high_low') {
                    $query->orderBy('price', 'desc');
                }
            }
        );
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
