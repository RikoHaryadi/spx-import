<?php

namespace App\Services;

use App\Models\MonitoringSummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringSummaryService
{
    public static function sync(): void
    {
        $start = microtime(true);

        Log::info('======================================');
        Log::info('MONITORING SUMMARY START');
        Log::info('======================================');

        DB::transaction(function () use ($start) {

            $summary = DB::table('monitoring_tracking')
    ->whereNotNull('current_driver_id')
    ->where('current_driver_id', '<>', '')
    ->where('current_driver_id', '<>', '0')
    ->selectRaw("
        operation_date,

        current_driver_id   as driver_id,
        current_driver_name as driver_name,

                    hub_id,

                    COUNT(*) as total,

                    SUM(
                        CASE
                            WHEN status='Delivered'
                            THEN 1 ELSE 0
                        END
                    ) as delivered,

                    SUM(
                        CASE
                            WHEN status IN ('OnHold','LMHub_Received')
                            THEN 1 ELSE 0
                        END
                    ) as onhold,

                    SUM(
                        CASE
                            WHEN status='Delivering'
                            THEN 1 ELSE 0
                        END
                    ) as delivering,

                    SUM(
                        CASE
                            WHEN payment_method='COD'
                            THEN 1 ELSE 0
                        END
                    ) as cod_total,

                    SUM(
                        CASE
                            WHEN payment_method='COD'
                             AND status='Delivered'
                            THEN 1 ELSE 0
                        END
                    ) as cod_delivered,

                    SUM(
                        CASE
                            WHEN payment_method<>'COD'
                            THEN 1 ELSE 0
                        END
                    ) as noncod_total,

                    SUM(
                        CASE
                            WHEN payment_method<>'COD'
                             AND status='Delivered'
                            THEN 1 ELSE 0
                        END
                    ) as noncod_delivered
                ")
                ->groupBy(
                    'operation_date',
                    'current_driver_id',
                    'current_driver_name',
                    'hub_id'
                )
                ->get();

            Log::info('Driver : '.$summary->count());
            Log::info(
'Delivered : '.$summary->sum('delivered')
);

Log::info(
'OnHold : '.$summary->sum('onhold')
);

Log::info(
'Delivering : '.$summary->sum('delivering')
);

// Log::info(
// 'Remaining : '.$upsertsRemaining
// );

            $upserts = [];

            foreach ($summary as $row) {

                $remaining = max(
                    0,
                    $row->total
                    - $row->delivered
                    - $row->onhold
                );

                $progress = $row->total
                    ? round(
                        ($row->delivered / $row->total) * 100,
                        2
                    )
                    : 0;

                if ($progress >= 98) {

                    $label = 'Excellent';
                    $color = 'success';

                } elseif ($progress >= 95) {

                    $label = 'Good';
                    $color = 'primary';

                } elseif ($progress >= 90) {

                    $label = 'Warning';
                    $color = 'warning';

                } else {

                    $label = 'Critical';
                    $color = 'danger';

                }

                $upserts[] = [

                    'operation_date' => $row->operation_date,

                    'driver_id' => $row->driver_id,

                    'driver_name' => $row->driver_name,

                    'hub_id' => $row->hub_id,

                    'total' => $row->total,

                    'delivered' => $row->delivered,

                    'onhold' => $row->onhold,

                    'delivering' => $row->delivering,

                    'remaining' => $remaining,

                    'progress' => $progress,

                    'status_label' => $label,

                    'status_color' => $color,

                    'cod_total' => $row->cod_total,

                    'cod_delivered' => $row->cod_delivered,

                    'noncod_total' => $row->noncod_total,

                    'noncod_delivered' => $row->noncod_delivered,

                    'created_at' => now(),

                    'updated_at' => now(),

                ];
            }

            MonitoringSummary::upsert(

                $upserts,

                [
                    'operation_date',
                    'driver_id',
                    'hub_id'
                ],

                [

                    'driver_name',

                    'hub_id',

                    'total',

                    'delivered',

                    'onhold',

                    'delivering',

                    'remaining',

                    'progress',

                    'status_label',

                    'status_color',

                    'cod_total',

                    'cod_delivered',

                    'noncod_total',

                    'noncod_delivered',

                    'updated_at'

                ]

            );

            Log::info('Summary : '.count($upserts));

            Log::info(
                'Time : '.
                round(
                    microtime(true)-$start,
                    2
                ).
                ' Seconds'
            );

            Log::info('======================================');
            Log::info('MONITORING SUMMARY FINISH');
            Log::info('======================================');

        });
    }
}