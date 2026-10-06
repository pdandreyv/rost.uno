<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $package = app('rostpack');

        Log::info('Rendering login page', [
            'is_telegram_webapp' => (bool) $request->attributes->get('isTelegramWebApp', false),
        ]);

        return view('rostpack.auth.login', [
            'package' => $package,
            'version' => $package->getVersion(),
        ]);
    }
}
