<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pizza;
use App\Models\Drink;

class ShowcaseController extends Controller
{
    public function index()
    {
        $pizzas = Pizza::paginate(10);
        $drinks = Drink::paginate(10);

        return view('showcase', compact('pizzas', 'drinks'));
    }
}
