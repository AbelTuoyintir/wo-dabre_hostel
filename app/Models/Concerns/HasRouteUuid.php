<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasRouteUuid
{
    protected static function bootHasRouteUuid(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        $model = $this->newQuery()->where($field, $value)->first();

        if ($model !== null) {
            return $model;
        }

        if ($field === $this->getRouteKeyName() && is_numeric($value)) {
            return $this->newQuery()->whereKey($value)->first();
        }

        return null;
    }
}
