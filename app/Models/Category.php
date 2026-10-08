<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property bool $is_active
 */
class Category extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: 'danh-muc';
        $count = 1;
        while (static::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = ($baseSlug ?: 'danh-muc') . '-' . $count;
            $count++;
        }
        return $slug;
    }

    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
