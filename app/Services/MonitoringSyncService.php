<?php

namespace App\Services;

use App\Models\MonitoringTracking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringSyncService
{
    public static function sync(array $rows, $hubId): void
    {
        $start = microtime(true);

        Log::info('======================================');
        Log::info('MONITORING SYNC START');
        Log::info('======================================');

        DB::transaction(function () use ($rows, $hubId, $start) {

            /*
            |--------------------------------------------------------------------------
            | Ambil seluruh AWB yang diupload
            |--------------------------------------------------------------------------
            */

            $orderIds = collect($rows)
                ->pluck('order_id')
                ->filter()
                ->unique()
                ->values();

            /*
            |--------------------------------------------------------------------------
            | Ambil data lama
            |--------------------------------------------------------------------------
            */

            $existing = MonitoringTracking::whereIn(
                    'order_id',
                    $orderIds
                )
                ->get()
                ->keyBy('order_id');

            $upserts = [];

            foreach ($rows as $row) {

                $old = $existing->get($row['order_id']);
                $driverId = trim((string)($row['driver_id'] ?? ''));
$driverName = trim((string)($row['driver_name'] ?? ''));

if ($driverId === '' || $driverId === '0') {
    $driverId = null;
}

if ($driverName === '') {
    $driverName = null;
}

                /*
                |--------------------------------------------------------------------------
                | Driver Awal
                |--------------------------------------------------------------------------
                */

$initialDriverId =
    $old?->initial_driver_id
    ?? $driverId;

$initialDriverName =
    $old?->initial_driver_name
    ?? $driverName;

                /*
                |--------------------------------------------------------------------------
                | Driver Sekarang
                |--------------------------------------------------------------------------
                */

$currentDriverId =
    $driverId
        ?? $old?->current_driver_id;

$currentDriverName =
    $driverName
        ?? $old?->current_driver_name;
                /*
                |--------------------------------------------------------------------------
                | Siapkan Upsert
                |--------------------------------------------------------------------------
                */

                $upserts[] = [

                    'order_id' => $row['order_id'],

                    'operation_date' => today(),

                    'hub_id' => $hubId,

                    'payment_method'=>trim(
    $row['payment_method']
),

                    'order_account' => $row['order_account'],

                   'status'=>trim($row['status']),

                    'received_time' => $row['received_time'],

                    'current_station_received_time'
                        => $row['current_station_received_time'],

                    'delivering_time'
                        => $row['delivering_time'],

                    'delivered_time'
                        => $row['delivered_time'],

                    'on_hold_time'
                        => $row['on_hold_time'],

                    'on_hold_reason'
                        => $row['on_hold_reason'],

                    'reschedule_date'
                        => $row['reschedule_date'],

                    'current_station'
                        => $row['current_station'],

                    /*
                    |--------------------------------------------------------------------------
                    | Driver
                    |--------------------------------------------------------------------------
                    */

                    'initial_driver_id'
                        => $initialDriverId,

                    'initial_driver_name'
                        => $initialDriverName,

                    'current_driver_id'
                        => $currentDriverId,

                    'current_driver_name'
                        => $currentDriverName,

                    'updated_at'
                        => now(),

                    'created_at'
                        => $old?->created_at ?? now(),

                ];

            }

            MonitoringTracking::upsert(

                $upserts,

                ['order_id'],

                [

                    'status',

                    'operation_date',

                    'hub_id',

                    'payment_method',

                    'order_account',

                    'received_time',

                    'current_station_received_time',

                    'delivering_time',

                    'delivered_time',

                    'on_hold_time',

                    'on_hold_reason',

                    'reschedule_date',

                    'current_station',

                    'current_driver_id',

                    'current_driver_name',

                    'updated_at',

                ]

            );

            Log::info(
                'UPSERT : '.count($upserts).' AWB'
            );

            Log::info(
                'TIME : '.round(
                    microtime(true)-$start,
                    2
                ).' Seconds'
            );

            Log::info('MONITORING SYNC FINISH');

        });

    }
}