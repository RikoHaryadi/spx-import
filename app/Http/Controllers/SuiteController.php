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
    
     \Log::info('=== SUITE IMPORT ===');
    \Log::info([
        'isJson' => $request->isJson(),
        'contentType' => $request->header('Content-Type'),
        'body' => $request->all(),
    ]);
/*
    |--------------------------------------------------------------------------
    | REQUEST DARI GOOGLE SHEET
    |--------------------------------------------------------------------------
    */

    if ($request->isJson()) {

        $rows = $request->input('data', []);

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data'
            ], 400);
        }

        SuiteData::upsert(

            $rows,

            ['shipment_id'],

            [

                'date_id',
                'lmhub_station_name',
                'inbound_group',
                'delivered_time',
                'transported_time',
                'assigned_delivering_time',
                'on_hold_count',
                'assigned_time',
                'last_on_hold_timestamp',
                'addr_zone_name',
                'driver_id',
                'within_cutoff_delivered',
                'within_cutoff_assigned',
                'within_assigned_delivering',
                'is_lmhub_delivery_transfer',
                'status',
                'updated_at'

            ]

        );

        \App\Jobs\SyncStdSummaryJob::dispatch();

        return response()->json([
            'success' => true,
            'rows' => count($rows)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REQUEST DARI WEBSITE (UPLOAD EXCEL)
    |--------------------------------------------------------------------------
    */

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
}

}