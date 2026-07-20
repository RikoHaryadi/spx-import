<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrackingData;
use App\Services\StdSummaryService;

class TrackingController extends Controller
{
   

 public function index()
    {
        $totalTracking = TrackingData::count();

        return view(
            'tracking.index',
            compact('totalTracking')
        );
    }

    public function import(Request $request)
    {
        if ($request->isJson()) {

    $rows = $request->input('data', []);

    if (empty($rows)) {
        return response()->json([
            'success'=>false
        ],400);
    }

    TrackingData::upsert(

        $rows,

        ['order_id'],

        [

            'driver_id',
            'driver_name',
            'received_time',
            'current_station_received_time',
            'delivering_time',
            'delivered_time',
            'on_hold_time',
            'on_hold_reason',
            'reschedule_date',
            'status',
            'order_account',
            'payment_method',
            'current_station',
            'updated_at'

        ]

    );

    \App\Jobs\SyncStdSummaryJob::dispatch();

    return response()->json([
        'success'=>true,
        'rows'=>count($rows)
    ]);

}
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

       $header = fgetcsv($file, 0, ',');

$header = array_map(function ($item) {
    return trim(preg_replace('/^\xEF\xBB\xBF/', '', $item));
}, $header);

        $insertData = [];

        while (($row = fgetcsv($file, 0, ',')) !== false) {

            $data = array_combine($header, $row);
$orderId = trim($data['Order ID'] ?? '');

if (empty($orderId)) {
    continue;
}
            $insertData[] = [

                'order_id' => $orderId,

                'driver_id' => $data['Driver ID'] ?? null,
                'driver_name' => $data['Driver Name'] ?? null,

                'received_time' => $this->emptyToNull($data['Received Time'] ?? null),

                'current_station_received_time' =>
                    $this->emptyToNull($data['Current Station Received Time'] ?? null),

                'delivering_time' =>
                    $this->emptyToNull($data['Delivering Time'] ?? null),

                'delivered_time' =>
                    $this->emptyToNull($data['Delivered Time'] ?? null),

                'on_hold_time' =>
                    $this->emptyToNull($data['OnHold Time'] ?? null),

                'on_hold_reason' =>
                    $data['OnHoldReason'] ?? null,

                'reschedule_date' =>
                    $this->emptyToNull($data['Reschedule Date'] ?? null),

                'status' =>
                    $data['Status'] ?? null,

                'order_account' =>
                    $data['Order Account'] ?? null,

                'payment_method' =>
                    $data['Payment Method'] ?? null,

                'current_station' =>
                    $data['Current Station'] ?? null,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($insertData) >= 1000) {
                TrackingData::insert($insertData);
                $insertData = [];
            }
        }

        if (!empty($insertData)) {
            TrackingData::insert($insertData);
        }

        fclose($file);
        // TrackingImport::import($file);

    StdSummaryService::sync();

        return back()->with(
            'success',
            'Import Tracking berhasil'
        );
        
        $total = TrackingData::count();
return redirect()
    ->route('tracking.index')
    ->with(
        'success',
        'Import berhasil. Total resi tracking saat ini : '
        . number_format($total)
    );
    }

    private function emptyToNull($value)
{
    $value = trim((string) $value);

    if ($value === '' || $value === '0') {
        return null;
    }

    return $value;
}
}