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

$rows = $this->normalizeRows($rows);

if (empty($rows)) {
    return response()->json([
        'success' => false,
        'message' => 'Tidak ada data'
    ],400);
}

\Log::info('Jumlah data diterima : '.count($rows));
\Log::info($rows[0]);

try {

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

} catch (\Throwable $e) {

    \Log::error('========== UPSERT ERROR ==========');
    \Log::error($e->getMessage());
    \Log::error($e->getTraceAsString());
    \Log::error($rows);

    throw $e;

}

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
private function normalizeRows(array $rows): array
{
    // kolom angka
    $numericFields = [
        'on_hold_count',
    ];
        // kolom boolean
    $booleanFields = [
        'is_lmhub_delivery_transfer',
    ];
    // kolom tanggal
    $dateFields = [
        'delivered_time',
        'transported_time',
        'assigned_delivering_time',
        'assigned_time',
        'last_on_hold_timestamp',
    ];

    foreach ($rows as &$row) {

        // pastikan updated_at selalu ada
        $row['created_at'] ??= now();
$row['updated_at'] = now();

        // angka
        foreach ($numericFields as $field) {

            if (!array_key_exists($field, $row) || trim((string)$row[$field]) === '') {
                $row[$field] = 0;
            } else {
                $row[$field] = (int)$row[$field];
            }
        }
      // boolean
        foreach ($booleanFields as $field) {

            if (!array_key_exists($field, $row)) {
                $row[$field] = 0;
            } else {

                $value = strtolower(trim((string)$row[$field]));

                $row[$field] = in_array($value, [
                    'yes',
                    'true',
                    '1'
                ]) ? 1 : 0;
            }
        }
        // tanggal
        foreach ($dateFields as $field) {

            if (!array_key_exists($field, $row) || trim((string)$row[$field]) === '') {
                $row[$field] = null;
            }
        }

        // text kosong → null
        foreach ($row as $key => $value) {

            if (is_string($value)) {

                $value = trim($value);

                $row[$key] = $value === '' ? null : $value;
            }
        }
    }

    return $rows;
}

}