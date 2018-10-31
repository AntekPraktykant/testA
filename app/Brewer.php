<?php

namespace App;

use App\Beer;
use Illuminate\Database\Eloquent\Model;

class Brewer extends Model
{
    public function beers()
    {
        return $this->hasMany(Beer::class);
    }
}
