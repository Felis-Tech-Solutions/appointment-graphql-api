<?php

namespace App\Casts;

use Cknow\Money\Money;
use Cknow\Money\Casts\MoneyDecimalCast;
use Illuminate\Database\Eloquent\Model;

class MoneyCast extends MoneyDecimalCast
{
    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        return [$key => Money::parse($value)->getAmount()];
    }
}
