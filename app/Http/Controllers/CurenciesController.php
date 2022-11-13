<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurenciesController extends Controller
{
    public function index()
    {
        $currencies = $this->getCurrencies();
        return view('currencies', ['currencies' =>  $currencies]);
    }

    private function getCurrencies()
    {
        return $currencies = DB::table('currencies_records')
            ->select('*')
            ->limit(1000)
            ->orderBy('date', 'DESC')
            ->get();
    }
}
