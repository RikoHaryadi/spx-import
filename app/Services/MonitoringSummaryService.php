<?php

namespace App\Services;

use App\Models\TrackingData;
use App\Models\MonitoringSummary;

class MonitoringSummaryService
{
    public static function sync($operationDate)
    {

      $drivers = TrackingData::selectRaw("
        hub_id,
        driver_id,
        driver_name,

        COUNT(*) as total,

        SUM(CASE WHEN status='Delivered' THEN 1 ELSE 0 END) delivered,

        SUM(CASE WHEN status='OnHold' THEN 1 ELSE 0 END) onhold
    ")
    ->where('data_source', 'monitoring')
    ->whereDate('operation_date', $operationDate)
    ->groupBy(
        'hub_id',
        'driver_id',
        'driver_name'
    )
    ->get();

        foreach ($drivers as $driver) {

            $remaining =
                $driver->total
                -
                $driver->delivered
                -
                $driver->onhold;

            $progress =
                $driver->total
                ?
                round(
                    ($driver->delivered / $driver->total) * 100,
                    2
                )
                :
                0;

            MonitoringSummary::updateOrCreate(
                

                [
                    'hub_id' => $driver->hub_id,

                    'operation_date' => $operationDate,

                    'driver_id' => $driver->driver_id,

                ],

                [
                    // 'hub_id' => $driver->hub_id,

                    'driver_name' => $driver->driver_name,

                    'total' => $driver->total,

                    'delivered' => $driver->delivered,

                    'onhold' => $driver->onhold,

                    'remaining' => $remaining,

                    'progress' => $progress,

                ]

            );
        }
    }
    private function statusLabel($progress)
{
    if ($progress >= 100) {
        return 'FINISH';
    }

    if ($progress >= 80) {
        return 'ON PROGRESS';
    }

    if ($progress >= 40) {
        return 'NEED ATTENTION';
    }

    return 'CRITICAL';
}

private function statusColor($progress)
{
    if ($progress >= 100) {
        return 'success';
    }

    if ($progress >= 80) {
        return 'primary';
    }

    if ($progress >= 40) {
        return 'warning';
    }

    return 'danger';
}
}