<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $source = $model->getSlugSourceColumn();

            if (empty($model->slug)) {
                $baseSlug = Str::slug($model->getAttribute($source));
                $model->slug = static::ensureUniqueSlug($model, $baseSlug);
            }
        });

        static::updating(function (Model $model) {
            $source = $model->getSlugSourceColumn();

            if ($model->isDirty($source) && ! $model->isDirty('slug')) {
                $oldSourceValue = $model->getOriginal($source);
                $expectedOldSlug = Str::slug($oldSourceValue);
                if ($model->slug === $expectedOldSlug || str_starts_with($model->slug, $expectedOldSlug.'-')) {
                    $baseSlug = Str::slug($model->getAttribute($source));
                    $model->slug = static::ensureUniqueSlug($model, $baseSlug);
                }
            }
        });
    }

    public function getSlugSourceColumn(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }

    /**
     * Ensure the slug is unique by appending a numeric suffix if necessary.
     */
    protected static function ensureUniqueSlug(Model $model, string $baseSlug): string
    {
        $slug = $baseSlug;
        $count = 1;

        while (static::slugExists($model, $slug)) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists in the database.
     */
    protected static function slugExists(Model $model, string $slug): bool
    {
        $query = $model->newQuery()->where('slug', $slug);

        if ($model->exists) {
            $query->where($model->getKeyName(), '!=', $model->getKey());
        }

        return $query->exists();
    }
}
