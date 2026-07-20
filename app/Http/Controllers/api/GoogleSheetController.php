<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuiteData;
use App\Models\TrackingData;
use App\Services\StdSummaryService;
use App\Jobs\SyncStdSummaryJob;
use Illuminate\Support\Facades\DB;

class GoogleSheetController extends Controller
{
    public function importSuite(Request $request)
    {
        $rows = $request->input('data', []);

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data'
            ]);
        }

        SuiteData::upsert(
            $rows,
            ['shipment_id'],
            [
                'date_id',
                'lmhub_station_name',
                'inbound_group',
                'delivered_time',
                'transported_time',
                'assigned_delivering_time',
                'on_hold_count',
                'assigned_time',
                'last_on_hold_timestamp',
                'addr_zone_name',
                'driver_id',
                'within_cutoff_delivered',
                'within_cutoff_assigned',
                'within_assigned_delivering',
                'is_lmhub_delivery_transfer',
                'status'
            ]
        );

        SyncStdSummaryJob::dispatch();

        return response()->json([
            'success' => true,
            'rows' => count($rows)
        ]);
    }

    public function importTracking(Request $request)
    {
        $rows = $request->input('data', []);

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data'
            ]);
        }

        TrackingData::upsert(
            $rows,
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
                'order_account',
                'payment_method',
                'current_station'
            ]
        );

        SyncStdSummaryJob::dispatch();

        return response()->json([
            'success' => true,
            'rows' => count($rows)
        ]);
    }
}