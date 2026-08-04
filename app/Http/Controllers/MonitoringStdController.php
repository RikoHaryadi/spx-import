<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\MonitoringTracking;
use App\Models\MonitoringSummary;
use App\Models\MonitoringUpdateLog;

use App\Services\HubContextService;
use App\Services\MonitoringSyncService;
use App\Services\MonitoringSummaryService;

class MonitoringStdController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Dashboard Monitoring
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $query = MonitoringSummary::whereDate(
            'operation_date',
            today()
        );

        HubContextService::apply($query);

        $drivers = $query
            ->orderByDesc('progress')
            ->orderByDesc('delivered')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $summary = [

            'driver'      => $drivers->count(),

            'total'       => $drivers->sum('total'),

            'delivered'   => $drivers->sum('delivered'),

            'onhold'      => $drivers->sum('onhold'),

            'remaining'   => $drivers->sum('remaining'),

            'progress'    => $drivers->sum('total')
                ? round(
                    (
                        $drivers->sum('delivered') +
                        $drivers->sum('onhold')
                    )
                    /
                    $drivers->sum('total')
                    *100,
                    2
                )
                :0,

            'achievement' => $drivers->sum('total')
                ? round(
                    $drivers->sum('delivered')
                    /
                    $drivers->sum('total')
                    *100,
                    2
                )
                :0,

        ];

        /*
        |--------------------------------------------------------------------------
        | Ranking
        |--------------------------------------------------------------------------
        */

        $topDrivers = $drivers
            ->sortByDesc('progress')
            ->take(5)
            ->values();

        $remainingDrivers = $drivers
            ->sortByDesc('remaining')
            ->take(5)
            ->values();

        $onHoldDrivers = $drivers
            ->sortByDesc('onhold')
            ->take(5)
            ->values();

        $fastestDrivers = $drivers
            ->sortByDesc('delivered')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Cek apakah sudah ada data Monitoring
        |--------------------------------------------------------------------------
        */

        $exists = MonitoringTracking::query();

        HubContextService::apply($exists);

        $hasMonitoring = $exists->exists();

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

    /*
    |--------------------------------------------------------------------------
    | Import CSV Monitoring
    |--------------------------------------------------------------------------
    */

    public function import(Request $request)
    {

        if(auth()->user()->role == 'viewer'){

            abort(
                403,
                'Viewer tidak memiliki hak upload Monitoring.'
            );

        }

        $request->validate([

            'file' => 'required|mimes:csv,txt'

        ]);

        $file = fopen(
            $request
                ->file('file')
                ->getRealPath(),
            'r'
        );

        $header = fgetcsv($file);

        $header = array_map(function($v){

            return trim(
                preg_replace(
                    '/^\xEF\xBB\xBF/',
                    '',
                    $v
                )
            );

        },$header);

        $rows=[];

        while(($row=fgetcsv($file))!==false){

            $data=array_combine(
                $header,
                $row
            );

            $orderId=trim(
                $data['Order ID'] ?? ''
            );

            if($orderId==''){
                continue;
            }

            $rows[]=[

                'order_id'=>$orderId,

                'driver_id'=>$data['Driver ID'] ?? null,

                'driver_name'=>$data['Driver Name'] ?? null,

                'received_time'=>$this->emptyToNull(
                    $data['Received Time'] ?? null
                ),

                'current_station_received_time'=>$this->emptyToNull(
                    $data['Current Station Received Time'] ?? null
                ),

                'delivering_time'=>$this->emptyToNull(
                    $data['Delivering Time'] ?? null
                ),

                'delivered_time'=>$this->emptyToNull(
                    $data['Delivered Time'] ?? null
                ),

                'on_hold_time'=>$this->emptyToNull(
                    $data['OnHold Time'] ?? null
                ),

                'on_hold_reason'=>$data['OnHoldReason'] ?? null,

                'reschedule_date'=>$this->emptyToNull(
                    $data['Reschedule Date'] ?? null
                ),

                'status'=>$data['Status'] ?? null,

                'payment_method'=>$data['Payment Method'] ?? null,

                'order_account'=>$data['Order Account'] ?? null,

                'current_station'=>$data['Current Station'] ?? null,

            ];

        }

        fclose($file);

        Log::info(
            'Monitoring import : '.count($rows).' rows'
        );

        /*
        |--------------------------------------------------------------------------
        | Jalankan Engine Monitoring Baru
        |--------------------------------------------------------------------------
        */

        MonitoringSyncService::sync(
            $rows,
            auth()->user()->hub_id
        );

        MonitoringSummaryService::sync();

        return back()->with(

            'success',

            count($rows).' resi berhasil diimport.'

        );

    }
    private function emptyToNull($value)
    {
        $value = trim((string) $value);

        return ($value === '' || $value === '0')
            ? null
            : $value;
    }

    public function driver($driverId)
{
    return $this->driverDetail($driverId);
}

public function driverDetail($driverId)
{
    $query = MonitoringTracking::where(
        'current_driver_id',
        $driverId
    )->whereDate(
        'operation_date',
        today()
    );

    HubContextService::apply($query);

    $allRows = $query->get();

    if ($allRows->isEmpty()) {
        abort(404);
    }

    $table = clone $query;

    if (request('status')) {

        if (strtolower(request('status')) == 'on hold') {

            $table->whereIn('status', [
                'OnHold',
                'LMHub_Received'
            ]);

        } else {

            $table->where(
                'status',
                request('status')
            );

        }

    }

    if (request('payment') == 'COD') {

        $table->where(
            'payment_method',
            'COD'
        );

    }

    if (strtoupper(request('payment')) == 'NONCOD') {

        $table->where(
            'payment_method',
            '!=',
            'COD'
        );

    }

    $rows = $table
        ->orderBy('status')
        ->get();

    $driver = $allRows->first();

    $summary = [

        'total' => $allRows->count(),

        'delivered' => $allRows
            ->where('status','Delivered')
            ->count(),

        'onhold' => $allRows
            ->whereIn('status',[
                'OnHold',
                'LMHub_Received'
            ])
            ->count(),

        'remaining' => $allRows
            ->where('status','Delivering')
            ->count(),

        'cod' => $allRows
            ->where('payment_method','COD')
            ->count(),

        'noncod' => $allRows
            ->where('payment_method','!=','COD')
            ->count(),

    ];

    $summary['achievement'] =
        $summary['total']
            ? round(
                ($summary['delivered']/$summary['total'])*100,
                2
            )
            :0;

    $summary['progress'] =
        $summary['total']
            ? round(
                (
                    $summary['delivered']
                    +
                    $summary['onhold']
                )
                /
                $summary['total']
                *100,
                2
            )
            :0;

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

        $tracking = MonitoringTracking::query();
        HubContextService::apply($tracking);
        $tracking->delete();

        $summary = MonitoringSummary::query();
        HubContextService::apply($summary);
        $summary->delete();
    });

    // Di luar transaction
    MonitoringUpdateLog::truncate();

    return back()->with(
        'success',
        'Monitoring berhasil dibersihkan.'
    );
}

private function isOnHold($row)
{
    return in_array($row->status,[
        'OnHold',
        'LMHub_Received'
    ]);
}

public function live()
{
    $query = MonitoringSummary::whereDate(
        'operation_date',
        today()
    );

    HubContextService::apply($query);

    $drivers = $query
        ->orderByDesc('progress')
        ->get();

    $summary = [

        'driver' => $drivers->count(),

        'total' => $drivers->sum('total'),

        'delivered' => $drivers->sum('delivered'),

        'onhold' => $drivers->sum('onhold'),

        'remaining' => $drivers->sum('remaining'),

        'achievement' => $drivers->sum('total')
            ? round(
                (
                    $drivers->sum('delivered')
                    /
                    $drivers->sum('total')
                ) * 100,
                2
            )
            :0,

        'progress' => $drivers->sum('total')
            ? round(
                (
                    (
                        $drivers->sum('delivered')
                        +
                        $drivers->sum('onhold')
                    )
                    /
                    $drivers->sum('total')
                ) * 100,
                2
            )
            :0,
    ];

return response()->json([
    'summary' => [
        'driver'      => $drivers->count(),
        'achievement' => $achievement,
        'progress'    => $progress,
    ],

    'grand' => [
        'total'      => $drivers->sum('total'),
        'delivered'  => $drivers->sum('delivered'),
        'onhold'     => $drivers->sum('onhold'),
        'remaining'  => $drivers->sum('remaining'),
    ],

    'drivers' => $drivers
]);
}

}