<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\SuiteImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SuiteData;

class SuiteController extends Controller
{
    public function index()
{
    $totalSuite = SuiteData::count();

    return view('suite.index', compact(
        'totalSuite'
    ));
}

   public function import(Request $request)
{
    Excel::import(
    new SuiteImport,
    $request->file('file')
);

$total = SuiteData::count();

return redirect()
    ->route('suite.index')
    ->with(
        'success',
        'Import berhasil. Total resi saat ini : '
        . number_format($total)
    );

    return back()
        ->with('success', 'Import berhasil');
}

}