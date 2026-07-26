<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContexHubController extends Controller
{
public function change(Request $request)
{
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
