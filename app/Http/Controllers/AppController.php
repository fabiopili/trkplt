<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use View;

class AppController extends Controller
{

    public function __construct()
    {

    }

    public function showResult()
    {
    	return view('public.main');
    }

    public function inject()
    {
    	return view('public.main');
    }

}