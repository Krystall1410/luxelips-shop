<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property string $type
 * @property float|null $price
 * @property int $stock
 * @property string|null $image
 * @property string|null $image_url
 * @property string|null $sku
 * @property string|null $color_description
 * @property string|null $hex_color
 * @property float $effective_price
 * @property \App\Models\Product $product
 * @property \App\Models\Category|null $category
 */
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'category_id', 'type', 'price', 'stock', 'image', 'sku', 'color_description', 'hex_color'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function getEffectivePriceAttribute()
    {
        return (!is_null($this->price) && $this->price > 0) 
            ? $this->price 
            : ($this->product ? $this->product->original_price : 0);
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        return asset('storage/' . $this->image);
    }
}
