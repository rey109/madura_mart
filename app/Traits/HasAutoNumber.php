<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Carbon\Carbon;

trait HasAutoNumber
{
    /**
     * Boot the trait to handle the "creating" event.
     */
    protected static function bootHasAutoNumber()
    {
        static::creating(function ($model) {
            $field = $model->getAutoNumberField();
            if (empty($model->{$field})) {
                $model->{$field} = static::generateAutoNumber($model->getAutoNumberPrefix());
            }
        });
    }

    /**
     * Generate a unique auto-number.
     * Format: PREFIX-YYYYMMDD-XXXX
     */
    public static function generateAutoNumber($prefix)
    {
        $date = Carbon::now()->format('ymd');
        $searchPrefix = $prefix . '-' . $date . '-';
        
        $lastRecord = static::where(static::getAutoNumberFieldStatic(), 'LIKE', $searchPrefix . '%')
            ->orderBy(static::getAutoNumberFieldStatic(), 'desc')
            ->first();

        $sequence = 1;
        if ($lastRecord) {
            $lastNumber = $lastRecord->{static::getAutoNumberFieldStatic()};
            $lastSequence = (int) substr($lastNumber, strrpos($lastNumber, '-') + 1);
            $sequence = $lastSequence + 1;
        }

        return $searchPrefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Define the field to be auto-numbered.
     */
    abstract public function getAutoNumberField(): string;

    /**
     * Static helper to get the field name.
     */
    protected static function getAutoNumberFieldStatic(): string
    {
        return (new static)->getAutoNumberField();
    }

    /**
     * Define the prefix for the auto-number.
     */
    abstract public function getAutoNumberPrefix(): string;
}
