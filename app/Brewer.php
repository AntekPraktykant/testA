<?php

namespace App;

use App\Beer;
use Illuminate\Database\Eloquent\Model;

class Brewer extends Model
{
    public $timestamps = false;

    public function beers()
    {
        return $this->hasMany(Beer::class);
    }
}
