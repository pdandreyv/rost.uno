<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Показать главную страницу
     */
    public function index()
    {
        return view('welcome');
    }
}
