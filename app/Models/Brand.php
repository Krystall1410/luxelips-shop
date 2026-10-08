<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $origin
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 */
class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'origin',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: 'thuong-hieu';
        $count = 1;
        while (static::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = ($baseSlug ?: 'thuong-hieu') . '-' . $count;
            $count++;
        }
        return $slug;
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }
}
