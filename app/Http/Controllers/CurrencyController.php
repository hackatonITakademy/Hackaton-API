<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currency = Currency::all();
        return new Response($currency, Response::HTTP_OK);
    }
}
