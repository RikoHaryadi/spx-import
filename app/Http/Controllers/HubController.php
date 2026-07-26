<?php

namespace App\Http\Controllers;
use App\Models\Hub;

use Illuminate\Http\Request;

class HubController extends Controller
{
public function index()
{
    $hubs = \App\Models\Hub::latest()->paginate(10);

    $summary = [

        'totalHub' => \App\Models\Hub::count(),

        'activeHub' => \App\Models\Hub::where('is_active',1)->count(),

        'region' => \App\Models\Hub::distinct('region')->count(),

        'user' => \App\Models\User::count(),

    ];

return view(
    'master.hub',
    compact(
        'hubs',
        'summary'
    )
);
}
    public function create()
    {
        return view('master.hub.create');
    }

    public function store(Request $request)
{
    $request->validate([

        'hub_code'=>'required|unique:hubs',

        'hub_name'=>'required',

    ]);

    Hub::create([

        'hub_code'=>$request->hub_code,

        'hub_name'=>$request->hub_name,

        'city'=>$request->city,

        'region'=>$request->region,

        'is_active'=>$request->has('is_active'),

    ]);

    return back()->with(
        'success',
        'Hub berhasil ditambahkan.'
    );
}

    public function edit(Hub $hub)
    {
        return view('master.hub.edit',compact('hub'));
    }

   public function update(Request $request, Hub $hub)
{
    $request->validate([

        'hub_code'=>'required|unique:hubs,hub_code,'.$hub->id,

        'hub_name'=>'required',

    ]);

    $hub->update([

        'hub_code'=>$request->hub_code,

        'hub_name'=>$request->hub_name,

        'city'=>$request->city,

        'region'=>$request->region,

        'is_active'=>$request->has('is_active'),

    ]);

    return back()->with(
        'success',
        'Hub berhasil diperbarui.'
    );
}
    public function destroy(Hub $hub)
    {
        $hub->delete();

        return back()->with('success','Hub berhasil dihapus');
    }

    
}
