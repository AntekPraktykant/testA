<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Cache;
use App\Beer;
use App\Brewer;

class BeersController extends Controller
{
    public function getBeers($console) {
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

            $console->info('All brewers fetched. (' . count($uniqueBreweries) . ' in total).');
            $console->info('All beers fetched. (' . count($uniqueBeers) . ' in total).');
        }

        catch (exception $e) {
            $console->info ("Unable to connect to remote URI ", $e->getMessage());
        }

    }//)->describe('Fetch Beers and Brewers from remote API');

    public function test(){
        return "test";
    }

    public function fromTo($priceFrom, $priceTo){
        if ((double)$priceFrom > (double)$priceTo) return "Please enter priceFrom > priceTo";

        return Beer::where('price', '>=', $priceFrom)->where('price', '<=', $priceTo);
    }

}
