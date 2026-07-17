<?php

namespace App\Services;

use App\Models\SuiteData;
use App\Models\TrackingData;
use App\Models\StdSummary;

class StdSummaryService
{
    public static function sync()
    {
        // Ambil seluruh Tracking sekali saja lalu jadikan index berdasarkan order_id
        $trackingMap = TrackingData::select(
            'order_id',
            'driver_id',
            'driver_name',
            'payment_method',
            'order_account',
            'on_hold_reason',
            'status'
        )->get()->keyBy('order_id');

        // Proses SuiteData per 1000 baris
        SuiteData::chunk(1000, function ($suiteRows) use ($trackingMap) {

            $upsertData = [];

            foreach ($suiteRows as $suite) {

                $tracking = $trackingMap->get($suite->shipment_id);

                $upsertData[] = [

                    'shipment_id'      => $suite->shipment_id,
                    'date_id'          => $suite->date_id,
                    'hub'              => $suite->lmhub_station_name,

                    'driver_id'        => $tracking->driver_id ?? null,
                    'driver_name'      => $tracking->driver_name ?? null,
                    'payment_method'   => $tracking->payment_method ?? null,
                    'order_account'    => $tracking->order_account ?? null,
                    'on_hold_reason'   => $tracking->on_hold_reason ?? null,
                    'tracking_status'  => $tracking->status ?? null,

                    'fifo_status'      => $suite->status,
                    'delivered_time'   => $suite->delivered_time,

                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }

            StdSummary::upsert(

                $upsertData,

                // Primary / Unique Key
                ['shipment_id'],

                // Kolom yang akan di-update jika shipment_id sudah ada
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
        });
    }
}