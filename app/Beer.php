<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beer extends Model
{
    public $timestamps = false;

    public static function boot() {
        parent::boot();

        $callback = function ($beer) {
            $beer->price_per_liter = $beer->price . $beer->size;
        };

        self::creating($callback);
        self::updating($callback);
    }
}
