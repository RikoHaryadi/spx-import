<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonitoringSummary;

class MonitoringDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->tanggal;

        $query = MonitoringSummary::query();

        if ($tanggal) {
            $query->whereDate('operation_date', $tanggal);
        }

        $drivers = $query
            ->orderByDesc('progress')
            ->orderByDesc('delivered')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Grand Total
        |--------------------------------------------------------------------------
        */

        $grand = [

            'total' => $drivers->sum('total'),

            'delivered' => $drivers->sum('delivered'),

            'onhold' => $drivers->sum('onhold'),

            'remaining' => $drivers->sum('remaining'),

            'cod_total' => $drivers->sum('cod_total'),

            'cod_delivered' => $drivers->sum('cod_delivered'),

            'noncod_total' => $drivers->sum('noncod_total'),

            'noncod_delivered' => $drivers->sum('noncod_delivered'),

        ];

        /*
        |--------------------------------------------------------------------------
        | Summary Dashboard
        |--------------------------------------------------------------------------
        */

     $summary = [

    'driver' => $drivers->count(),

    'achievement' => $grand['total'] > 0
        ? round(
            ($grand['delivered'] / $grand['total']) * 100,
            2
        )
        : 0,

    'progress' => $grand['total'] > 0
        ? round(
            (
                $grand['delivered']
                +
                $grand['onhold']
            )
            /
            $grand['total']
            * 100,
            2
        )
        : 0,

];

        /*
        |--------------------------------------------------------------------------
        | Ranking Driver
        |--------------------------------------------------------------------------
        */

        $ranking = $drivers
            ->sortByDesc('progress')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Top Driver
        |--------------------------------------------------------------------------
        */

        $topDrivers = $drivers
            ->sortByDesc('progress')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Remaining Driver
        |--------------------------------------------------------------------------
        */

        $remainingDrivers = $drivers
            ->sortByDesc('remaining')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | On Hold Driver
        |--------------------------------------------------------------------------
        */

        $onHoldDrivers = $drivers
            ->sortByDesc('onhold')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Delivered Driver
        |--------------------------------------------------------------------------
        */

        $fastestDrivers = $drivers
            ->sortByDesc('delivered')
            ->take(5)
            ->values();

        return view(
            'monitoring.index',
            compact(
                'drivers',
                'ranking',
                'summary',
                'grand',
                'topDrivers',
                'remainingDrivers',
                'onHoldDrivers',
                'fastestDrivers',
                'tanggal'
            )
        );
    }

    public function live(Request $request)
{
    $tanggal = $request->tanggal;

    $query = MonitoringSummary::query();

    if ($tanggal) {
        $query->whereDate('operation_date', $tanggal);
    }

    $drivers = $query
        ->orderByDesc('progress')
        ->orderBy('driver_name')
        ->get();

    $grand = [

        'total' => $drivers->sum('total'),

        'delivered' => $drivers->sum('delivered'),

        'onhold' => $drivers->sum('onhold'),

        'remaining' => $drivers->sum('remaining'),

        'cod_total' => $drivers->sum('cod_total'),

        'cod_delivered' => $drivers->sum('cod_delivered'),

        'noncod_total' => $drivers->sum('noncod_total'),

        'noncod_delivered' => $drivers->sum('noncod_delivered'),

    ];

   $summary = [

    'driver' => $drivers->count(),

    'achievement' => $grand['total'] > 0
        ? round(
            ($grand['delivered'] / $grand['total']) * 100,
            2
        )
        : 0,

    'progress' => $grand['total'] > 0
        ? round(
            (
                $grand['delivered']
                +
                $grand['onhold']
            )
            /
            $grand['total']
            * 100,
            2
        )
        : 0,

];

    return response()->json([

        'summary' => $summary,

        'grand' => $grand,

        'drivers' => $drivers,

    ]);
}
}