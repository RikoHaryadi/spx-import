<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
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
    foreach ($rows as &$row) {

    $row['received_time'] =
        $this->normalizeDate($row['received_time'] ?? null);

    $row['current_station_received_time'] =
        $this->normalizeDate($row['current_station_received_time'] ?? null);

    $row['delivering_time'] =
        $this->normalizeDate($row['delivering_time'] ?? null);

    $row['delivered_time'] =
        $this->normalizeDate($row['delivered_time'] ?? null);

    $row['on_hold_time'] =
        $this->normalizeDate($row['on_hold_time'] ?? null);

    $row['reschedule_date'] =
        $this->normalizeDate($row['reschedule_date'] ?? null);

    // kosongkan string kosong
    foreach ($row as $key => $value) {
        if (is_string($value)) {
            $value = trim($value);
            $row[$key] = $value === '' ? null : $value;
        }
    }
$row['data_source'] = 'tracking';

$row['operation_date'] =
    !empty($row['delivered_time'])
        ? substr($row['delivered_time'],0,10)
        : now()->toDateString();

$row['created_at'] = now();

$row['updated_at'] = now();
    $row['created_at'] = now();
    $row['updated_at'] = now();
}
\Log::info('=== TRACKING AFTER NORMALIZE ===');
\Log::info($rows[0]);
    foreach ($rows as $row) {

    $tracking = TrackingData::firstOrNew([
        'order_id'    => $row['order_id'],
        'data_source' => 'tracking',
    ]);

    /*
    |--------------------------------------------------------------------------
    | IDENTITAS DRIVER
    |--------------------------------------------------------------------------
    | Hanya disimpan pertama kali.
    | Jangan pernah ditimpa jika upload berikutnya kosong.
    */

    if (empty($tracking->driver_id) && !empty($row['driver_id'])) {
        $tracking->driver_id = $row['driver_id'];
    }

    if (empty($tracking->driver_name) && !empty($row['driver_name'])) {
        $tracking->driver_name = $row['driver_name'];
    }

    /*
    |--------------------------------------------------------------------------
    | FIELD YANG SELALU DIUPDATE
    |--------------------------------------------------------------------------
    */

    $tracking->received_time                 = $row['received_time'];
    $tracking->current_station_received_time = $row['current_station_received_time'];
    $tracking->delivering_time               = $row['delivering_time'];
    $tracking->delivered_time                = $row['delivered_time'];
    $tracking->on_hold_time                  = $row['on_hold_time'];
    $tracking->on_hold_reason                = $row['on_hold_reason'];
    $tracking->reschedule_date               = $row['reschedule_date'];
    $tracking->status                        = $row['status'];
    $tracking->order_account                 = $row['order_account'];
    $tracking->payment_method                = $row['payment_method'];
    $tracking->current_station               = $row['current_station'];
    $tracking->operation_date                = $row['operation_date'];
    $tracking->data_source                   = 'tracking';

    $tracking->save();
}

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
                    'data_source'=>'monitoring',

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

private function normalizeDate($value)
{
    if ($value === null) {
        return null;
    }

    $value = trim((string) $value);

    if ($value === '' || $value === '0') {
        return null;
    }

    $formats = [
        'd/m/Y H:i',
        'd/m/Y H:i:s',
        'Y-m-d H:i',
        'Y-m-d H:i:s',
    ];

    foreach ($formats as $format) {
        try {
            return Carbon::createFromFormat($format, $value)
                ->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // coba format berikutnya
        }
    }

    return null;
}

}