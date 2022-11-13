<?php

namespace App\Http\Controllers;
use App\Parser\CurrencyParser;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    private $parser;

    public function index()
    {
        $this->parser = new CurrencyParser();
        return $this->parser->parseCurrencies();
    }
}
