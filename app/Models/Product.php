<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $sku
 * @property float $original_price
 * @property string|null $description
 * @property bool $is_active
 * @property string $price_range_formatted
 * @property int $total_stock
 * @property string|null $first_image
 * @property string|null $first_image_url
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories_list
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\ProductVariant[] $variants
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'sku', 'original_price', 'description', 'brand_id', 'uses', 'ingredients', 'is_active'];

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = \Illuminate\Support\Str::slug($name);
        $slug = $baseSlug ?: 'san-pham';
        $count = 1;
        while (static::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = ($baseSlug ?: 'san-pham') . '-' . $count;
            $count++;
        }
        return $slug;
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalStockAttribute()
    {
        return $this->variants->sum('stock');
    }

    public function getFirstImageAttribute()
    {
        $firstWithImage = $this->variants->first(fn($v) => !empty($v->image));
        return $firstWithImage ? $firstWithImage->image : null;
    }

    public function getFirstImageUrlAttribute()
    {
        $firstWithImage = $this->variants->first(fn($v) => !empty($v->image));
        return $firstWithImage ? $firstWithImage->image_url : null;
    }

    public function getCategoriesListAttribute()
    {
        return $this->variants
            ->pluck('category')
            ->filter()
            ->unique('id');
    }

    public function getPriceRangeFormattedAttribute()
    {
        $prices = $this->variants->map(fn($v) => $v->effective_price)->filter();
        if ($prices->isEmpty()) {
            return number_format($this->original_price, 0, ',', '.') . ' ₫';
        }
        $min = $prices->min();
        $max = $prices->max();
        if ($min == $max) {
            return number_format($min, 0, ',', '.') . ' ₫';
        }
        return number_format($min, 0, ',', '.') . ' ₫ - ' . number_format($max, 0, ',', '.') . ' ₫';
    }
}
