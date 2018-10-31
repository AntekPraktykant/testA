<?php

use App\Beer;
use App\Brewer;
use App\Http\Controllers\BeersController;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('get:brewers', function () {
    try{
//get JSON
        $beers = Cache::remember('beers', 60, function () {
            return json_decode(file_get_contents('http://ontariobeerapi.ca/beers/'));
        });$dbBreweries = Brewer::all();

//unique brew and return array
        $uniqueBreweries = collect($beers)
            ->map->brewer
            ->unique()
            ->filter(function ($brewer) use ($dbBreweries) {
                return !$dbBreweries->map->name->contains($brewer);
            })
            ->map(function ($brewer) {
                return ['name' => $brewer];
            })
            ->toArray();

//insert into db
        DB::table('brewers')->insert($uniqueBreweries);

        $dbBreweries = Brewer::all();

        $dbBeers = Beer::all();

        $uniqueBeers = collect($beers)
            ->filter(function ($beer) use ($dbBeers) {
                return !$dbBeers->contains('product_id', $beer->product_id);
            })
            ->map(function ($beer) use ($dbBreweries) {
                $beer->brewer_id = $dbBreweries->filter(function ($brewer) use ($beer) {
                    return $brewer->name == $beer->brewer;
                })->first()->id;

                unset($beer->brewer);

                return (array) $beer;
            })
            ->toArray();

        DB::table('beers')->insert($uniqueBeers);

        $this->info('All brewers fetched. (' . count($uniqueBreweries) . ' in total).');
        $this->info('All beers fetched. (' . count($uniqueBeers) . ' in total).');
        }

    catch (exception $e) {
        $this->info ("Unable to connect to remote URI ", $e->getMessage());
    }

})->describe('Fetch Beers and Brewers from remote API');

Artisan::command('get:beers', function (BeersController $BC){
    return $BC->getBeers();

});
