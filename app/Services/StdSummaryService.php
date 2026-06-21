<?php

namespace App\Services;

use App\Models\SuiteData;
use App\Models\TrackingData;
use App\Models\StdSummary;

class StdSummaryService
{
    public static function sync()
    {
        $suiteRows = SuiteData::all();

        foreach ($suiteRows as $suite) {

            $tracking = TrackingData::where(
                'order_id',
                $suite->shipment_id
            )->first();

            StdSummary::updateOrCreate(

                [
                    'shipment_id' => $suite->shipment_id
                ],

                [
                    'date_id' => $suite->date_id,
                    'hub' => $suite->lmhub_station_name,

                    'driver_id' =>
                        $tracking->driver_id ?? null,

                    'driver_name' =>
                        $tracking->driver_name ?? null,

                    'payment_method' =>
                        $tracking->payment_method ?? null,

                    'order_account' =>
                        $tracking->order_account ?? null,

                    'on_hold_reason' =>
                        $tracking->on_hold_reason ?? null,

                    'tracking_status' =>
                        $tracking->status ?? null,

                    'fifo_status' =>
                        $suite->status,

                    'delivered_time' =>
                        $suite->delivered_time,
                ]
            );
        }
    }
}