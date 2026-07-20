<?php

namespace App\Services;

use App\Models\SuiteData;
use App\Models\TrackingData;
use App\Models\StdSummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StdSummaryService
{
    public static function sync()
    {
        $start = microtime(true);

        Log::info('==============================');
        Log::info('STD SUMMARY SYNC START');
        Log::info('==============================');

        $totalProcessed = 0;

        SuiteData::orderBy('id')
            ->chunkById(1000, function ($suiteChunk) use (&$totalProcessed) {

                DB::transaction(function () use ($suiteChunk, &$totalProcessed) {

                    /*
                    |--------------------------------------------------------------------------
                    | Ambil shipment pada chunk ini
                    |--------------------------------------------------------------------------
                    */

                    $shipmentIds = $suiteChunk
                        ->pluck('shipment_id')
                        ->toArray();

                    /*
                    |--------------------------------------------------------------------------
                    | Ambil Tracking yang memang dibutuhkan saja
                    |--------------------------------------------------------------------------
                    */

                    $trackingCollection = TrackingData::whereIn(
                        'order_id',
                        $shipmentIds
                    )
                    ->select(
                        'order_id',
                        'driver_id',
                        'driver_name',
                        'payment_method',
                        'order_account',
                        'on_hold_reason',
                        'status'
                    )
                    ->get()
                    ->keyBy('order_id');

                    $upserts = [];

                    foreach ($suiteChunk as $suite) {

                        $tracking = $trackingCollection->get(
                            $suite->shipment_id
                        );

                        $upserts[] = [

                            'shipment_id' => $suite->shipment_id,

                            'date_id' => $suite->date_id,

                            'hub' => $suite->lmhub_station_name,

                            'driver_id' => $tracking?->driver_id,

                            'driver_name' => $tracking?->driver_name,

                            'payment_method' => $tracking?->payment_method,

                            'order_account' => $tracking?->order_account,

                            'on_hold_reason' => $tracking?->on_hold_reason,

                            'tracking_status' => $tracking?->status,

                            'fifo_status' => $suite->status,

                            'delivered_time' => $suite->delivered_time,

                            'created_at' => now(),

                            'updated_at' => now()

                        ];

                    }

                    StdSummary::upsert(

                        $upserts,

                        ['shipment_id'],

                        [

                            'date_id',
                            'hub',
                            'driver_id',
                            'driver_name',
                            'payment_method',
                            'order_account',
                            'on_hold_reason',
                            'tracking_status',
                            'fifo_status',
                            'delivered_time',
                            'updated_at'

                        ]

                    );

                    $totalProcessed += count($upserts);

                    Log::info(
                        "Processed : {$totalProcessed}"
                    );

                });

            });

        $seconds = round(microtime(true) - $start,2);

        Log::info('==============================');
        Log::info("SYNC FINISH");
        Log::info("TOTAL : {$totalProcessed}");
        Log::info("TIME : {$seconds} Seconds");
        Log::info('==============================');
    }
}