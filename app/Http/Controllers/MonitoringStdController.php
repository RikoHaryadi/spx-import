<?php

namespace App\Http\Controllers;
use App\Models\TrackingData;
use Illuminate\Http\Request;
use App\Services\MonitoringSummaryService;
use App\Models\MonitoringSummary;
use Illuminate\Support\Facades\DB;
use App\Services\HubContextService;
class MonitoringStdController extends Controller
{
    public function index()
{
$query = MonitoringSummary::whereDate(
    'operation_date',
    today()
);

HubContextService::apply($query);

$drivers = $query
    ->orderByDesc('progress')
    ->get();



$drivers = $query
    ->orderByDesc('progress')
    ->get();

$topDrivers = $drivers
    ->sortByDesc('progress')
    ->take(5);

$remainingDrivers = $drivers
    ->sortByDesc('remaining')
    ->take(5);

$onHoldDrivers = $drivers
    ->sortByDesc('onhold')
    ->take(5);

$fastestDrivers = $drivers
    ->sortByDesc('delivered')
    ->take(5);



$summary = [

    'driver' => $drivers->count(),

    'total' => $drivers->sum('total'),

    'delivered' => $drivers->sum('delivered'),

    'onhold' => $drivers->sum('onhold'),

    'remaining' => $drivers->sum('remaining'),

    'progress' => $drivers->count()
    ? round($drivers->avg('progress'), 2)
    : 0,

];
$topDrivers = $drivers
    ->sortByDesc('progress')
    ->take(5);

$remainingDrivers = $drivers
    ->sortByDesc('remaining')
    ->take(5);

$onHoldDrivers = $drivers
    ->sortByDesc('onhold')
    ->take(5);

$fastestDrivers = $drivers
    ->sortByDesc('delivered')
    ->take(5);

$query = TrackingData::where(
    'data_source',
    'monitoring'
);

if(auth()->user()->role != 'owner'){

    $query->where(
        'hub_id',
        auth()->user()->hub_id
    );

}

$hasMonitoring = $query->exists();
return view(
    'monitoring.index',
    compact(
        'drivers',
        'summary',
        'topDrivers',
        'remainingDrivers',
        'onHoldDrivers',
        'fastestDrivers',
        'hasMonitoring'
    )
);
}

  public function import(Request $request)
{
        if (auth()->user()->role === 'viewer') {

        abort(403, 'Viewer tidak memiliki hak upload Monitoring.');

    }
    $request->validate([
        'file' => 'required|mimes:csv,txt'
    ]);



    $file = fopen(
        $request->file('file')->getRealPath(),
        'r'
    );

    $header = fgetcsv($file, 0, ',');

    $header = array_map(function ($v) {
        return trim(preg_replace('/^\xEF\xBB\xBF/', '', $v));
    }, $header);

    $rows = [];

while (($row = fgetcsv($file,0,',')) !== false) {

    $data = array_combine($header,$row);
    if (count($rows) == 0) {
    \Log::info($data);
}

    $orderId = trim($data['Order ID'] ?? '');

    if ($orderId == '') {
        continue;
    }

    $rows[] = [

        'order_id'=>$orderId,

        'driver_id'=>$data['Driver ID'] ?? null,

        'driver_name'=>$data['Driver Name'] ?? null,

        'received_time'=>$this->emptyToNull($data['Received Time'] ?? null),

        'current_station_received_time'=>$this->emptyToNull($data['Current Station Received Time'] ?? null),

        'delivering_time'=>$this->emptyToNull($data['Delivering Time'] ?? null),

        'delivered_time'=>$this->emptyToNull($data['Delivered Time'] ?? null),

        'on_hold_time'=>$this->emptyToNull($data['OnHold Time'] ?? null),

        'on_hold_reason'=>$data['OnHoldReason'] ?? null,

        'reschedule_date'=>$this->emptyToNull($data['Reschedule Date'] ?? null),

        'status'=>$data['Status'] ?? null,

        'order_account'=>$data['Order Account'] ?? null,

        'payment_method'=>$data['Payment Method'] ?? null,

        'current_station'=>$data['Current Station'] ?? null,
        

        //
        // INI YANG MEMBEDAKAN DENGAN TRACKING BIASA
        //
        'hub_id'=>auth()->user()->hub_id,
        'operation_date'=>today(),

        'data_source'=>'monitoring',

        'created_at'=>now(),

        'updated_at'=>now()

    ];

}
fclose($file);


\Log::info('Total rows : '.count($rows));


foreach (array_chunk($rows, 500) as $chunk) {

    \Log::info('Import chunk : '.count($chunk));

    TrackingData::upsert(

       $chunk,

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
        'operation_date',
        'data_source',
        'order_account',
        'payment_method',
        'current_station',

        'hub_id',      // ← WAJIB

        'updated_at'

    ]

);

}

MonitoringSummary::whereDate('operation_date', today())
    ->where('hub_id', auth()->user()->hub_id)
    ->delete();

MonitoringSummaryService::sync(today());

return back()->with(

    'success',

    'Monitoring berhasil diimport : '

    .count($rows)

    .' resi'

);
}
private function emptyToNull($value)
{
    $value = trim((string)$value);

    if ($value === '' || $value === '0') {
        return null;
    }

    return $value;
}
public function driver($driver)
{

   return $this->driverDetail($driver);

}

public function driverDetail($driverId)
{
    $operationDate = today();

    // Query dasar (SEMUA paket driver)
    $baseQuery = TrackingData::where(
        'driver_id',
        $driverId
    )
    ->where(
        'data_source',
        'monitoring'
    )
    ->whereDate(
        'operation_date',
        $operationDate
    );

    HubContextService::apply($baseQuery);

    // Ambil semua data driver untuk menghitung summary
    $allRows = (clone $baseQuery)->get();

    // Kalau driver memang tidak ada sama sekali baru 404
    if ($allRows->isEmpty()) {
        abort(404);
    }

    // Query untuk tabel (boleh difilter)
    $query = clone $baseQuery;

    if (request('status')) {
        $query->where('status', request('status'));
    }

    if (request('payment') == 'COD') {
        $query->where('payment_method', 'COD');
    }

    if (request('payment') == 'NONCOD') {
        $query->where('payment_method', '!=', 'COD');
    }

    $rows = $query
        ->orderBy('status')
        ->get();

    $driver = $allRows->first();

    $summary = [

        'total' => $allRows->count(),

        'delivered' => $allRows->where('status', 'Delivered')->count(),

        'onhold' => $allRows->where('status', 'On Hold')->count(),

    ];

    $summary['cod'] =
        $allRows->where('payment_method','COD')->count();

    $summary['noncod'] =
        $summary['total'] -
        $summary['cod'];

    $summary['remaining'] =
        $summary['total'] -
        $summary['delivered'] -
        $summary['onhold'];

    $summary['progress'] =
        $summary['total']
            ? round(($summary['delivered']/$summary['total'])*100,1)
            : 0;

    return view(
        'monitoring.driver-detail',
        compact(
            'driver',
            'rows',
            'summary'
        )
    );
}
public function reset()
{
    DB::transaction(function () {

        $tracking = TrackingData::where(
            'data_source',
            'monitoring'
        );

        HubContextService::apply($tracking);

        $tracking->delete();

        $summary = MonitoringSummary::query();

        HubContextService::apply($summary);

        $summary->delete();

    });

    return redirect()
        ->route('monitoring.index')
        ->with(
            'success',
            'Monitoring berhasil dibersihkan.'
        );
}

}