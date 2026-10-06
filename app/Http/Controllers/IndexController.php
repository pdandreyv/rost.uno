<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $isTelegramWebApp = (bool) $request->attributes->get('isTelegramWebApp', false);
        $package = app('rostpack');

        if (!Auth::check()) {
            Log::info('[tg-auth] guest on home', [
                'is_telegram_webapp' => $isTelegramWebApp,
                'path' => $request->path(),
            ]);

            // Браузер — форма входа с виджетом; Telegram — та же страница, JS сделает auto-auth
            return view('rostpack.auth.login', [
                'package' => $package,
                'version' => $package->getVersion(),
            ]);
        }

        return view('rostpack.index');
    }
}
