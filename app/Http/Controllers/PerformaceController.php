<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Hub;
use App\Services\StdSummaryService;

class PerformanceController extends Controller
{
    public function kurir(Request $request)
    {
        $hubs = Hub::all();
        $data = [];

        if ($request->date && $request->hub) {

            $data = DB::table('tracking')
                ->select(
                    'driver_id',
                    'driver_name',
                    DB::raw('COUNT(*) as total_std'),
                    DB::raw("SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as berhasil"),
                    DB::raw("SUM(CASE WHEN status != 'success' THEN 1 ELSE 0 END) as tidak_berhasil")
                )
                ->whereDate('created_at', $request->date)
                ->where('hub_id', $request->hub)
                ->groupBy('driver_id', 'driver_name')
                ->get();
        }

        return view('performance.kurir', compact('hubs', 'data'));
    }
}