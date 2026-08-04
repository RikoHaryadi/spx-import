<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ContexHubController extends Controller
{
public function change(Request $request)
{
        $user = auth()->user();

    Log::info('HUB FILTER',[
        'user' => $user?->name,
        'role' => $user?->role,
        'hub_id' => $user?->hub_id,
        'session' => session('hub_context')
    ]);

    if ($request->filled('hub_id')) {

        session([
            'hub_context' => $request->hub_id
        ]);

    } else {

        session()->forget('hub_context');

    }

    return back();
}
}
