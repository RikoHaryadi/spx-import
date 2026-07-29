<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\StdSummary;

class DashboardController extends Controller
{
  public function index(Request $request)
{
    $tanggal = $request->tanggal;

    $query = DB::table('suite_data as s')
        ->leftJoin('tracking_data as t', function ($join) {

    $join->on('s.shipment_id', '=', 't.order_id')
         ->where('t.data_source', '=', 'tracking');

})
        ->selectRaw("
            s.date_id,
            s.lmhub_station_name,
            COUNT(s.shipment_id) as total_std,
            SUM(CASE WHEN s.delivered_time IS NOT NULL THEN 1 ELSE 0 END) as berhasil,
            SUM(CASE WHEN s.delivered_time IS NULL THEN 1 ELSE 0 END) as tidak_berhasil
        ")
        ->whereIn('t.order_account', [
            'SPX Standard Marketplace',
            'SPX Standard',
            'NS Marketplace Standard'
        ]);

    if ($tanggal) {
        $query->whereDate('s.date_id', $tanggal);
    }

    $data = $query
    
        ->groupBy('s.date_id', 's.lmhub_station_name')
        ->orderByDesc('s.date_id')
        ->get();
 

    foreach ($data as $row) {
        $row->persentase = $row->total_std > 0
            ? round(($row->berhasil / $row->total_std) * 100, 2)
            : 0;
    }

$topHub = $data
    ->sortByDesc('persentase')
    ->take(10);
    $grand_total_std = $data->sum('total_std');
    $grand_berhasil = $data->sum('berhasil');
    $grand_tidak_berhasil = $data->sum('tidak_berhasil');

    $grand_persen = $grand_total_std > 0
        ? round(($grand_berhasil / $grand_total_std) * 100, 2)
        : 0;

    return view('dashboard.index', compact(
    'data',
    'tanggal',
    'grand_total_std',
    'grand_berhasil',
    'grand_tidak_berhasil',
    'grand_persen',
    'topHub'
));
}
  public function detail(Request $request)
{
    $date = $request->date;
    $hub  = $request->hub;

    $driverMaster = DB::table('tracking_data')
        ->select(
            'driver_id',
            DB::raw('MAX(driver_name) as driver_name')
        )
        ->whereNotNull('driver_name')
        ->where('driver_name', '<>', '')
        ->groupBy('driver_id');

    $data = DB::table('suite_data as s')
        ->leftJoin('tracking_data as t', function ($join) {

    $join->on('s.shipment_id', '=', 't.order_id')
         ->where('t.data_source', '=', 'tracking');

})

        ->leftJoinSub($driverMaster, 'd', function ($join) {
            $join->on('s.driver_id', '=', 'd.driver_id');
        })

->select(
    's.shipment_id',
    's.date_id',
    's.lmhub_station_name',
    's.driver_id',
    's.assigned_time',      // Tambahkan ini
    'd.driver_name',
    
    't.on_hold_reason',
    't.payment_method',
    't.order_account'
)

        ->where('s.date_id', $date)
        ->where('s.lmhub_station_name', $hub)

        ->whereIn('t.order_account', [
            'SPX Standard Marketplace',
            'SPX Standard',
            'NS Marketplace Standard'
        ])

        ->whereNull('s.delivered_time')
        ->orderBy('d.driver_name')
        ->paginate(100);
   foreach ($data as $row) {

    $row->display_reason = empty($row->assigned_time)
        ? 'Stuck Received'
        : ($row->on_hold_reason ?: '-');
}

return view(
    'dashboard.detail',
    compact('data', 'date', 'hub')
);
}
public function kurirPerformance(Request $request)
{
    $date = $request->date;
    $hub  = $request->hub;

    // 🔥 ambil list hub dari data existing
    $hubs = DB::table('suite_data')
        ->select('lmhub_station_name')
        ->whereNotNull('lmhub_station_name')
        ->distinct()
        ->orderBy('lmhub_station_name')
        ->get();

    $data = collect();

    if ($date && $hub) {

        $query = DB::table('suite_data as s')
            ->leftJoin('tracking_data as t', function ($join) {

    $join->on('s.shipment_id', '=', 't.order_id')
         ->where('t.data_source', '=', 'tracking');

})
            ->selectRaw("
                s.driver_id,
                COUNT(s.shipment_id) as total_std,
                SUM(CASE WHEN s.delivered_time IS NOT NULL THEN 1 ELSE 0 END) as berhasil,
                SUM(CASE WHEN s.delivered_time IS NULL THEN 1 ELSE 0 END) as tidak_berhasil
            ")
            ->where('s.date_id', $date)
            ->where('s.lmhub_station_name', $hub)
            ->whereIn('t.order_account', [
                'SPX Standard Marketplace',
                'SPX Standard',
                'NS Marketplace Standard'
            ])
            ->groupBy('s.driver_id');

        $raw = $query->get();

        $driverNames = DB::table('tracking_data')
            ->select('driver_id', DB::raw('MAX(driver_name) as driver_name'))
            ->groupBy('driver_id')
            ->pluck('driver_name', 'driver_id');

        $data = $raw->map(function ($row) use ($driverNames) {

            $row->driver_name = $driverNames[$row->driver_id] ?? '-';

            $row->persentase = $row->total_std > 0
                ? round(($row->berhasil / $row->total_std) * 100, 2)
                : 0;

            return $row;
        });
    }

    return view('performance.kurir', compact('data', 'date', 'hub', 'hubs'));
}
public function live(Request $request)
{
    $tanggal = $request->tanggal;

    $query = DB::table('std_summary')
        ->selectRaw("
            date_id,
            hub,
            COUNT(*) as total_std,
            SUM(CASE WHEN delivered_time IS NOT NULL THEN 1 ELSE 0 END) berhasil,
            SUM(CASE WHEN delivered_time IS NULL THEN 1 ELSE 0 END) tidak_berhasil
        ")
        ->whereIn('order_account', [
            'SPX Standard Marketplace',
            'SPX Standard',
            'NS Marketplace Standard'
        ]);

    if ($tanggal) {
        $query->whereDate('date_id', $tanggal);
    }

    $rows = $query
        ->groupBy('date_id', 'hub')
        ->orderByDesc('date_id')
        ->get();

    foreach ($rows as $r) {

        $r->persentase =
            $r->total_std
                ? round(($r->berhasil/$r->total_std)*100,2)
                :0;

    }

    return response()->json([

        "summary"=>[
            "total"=>$rows->sum('total_std'),
            "berhasil"=>$rows->sum('berhasil'),
            "gagal"=>$rows->sum('tidak_berhasil'),
            "persen"=>$rows->sum('total_std')
                ? round(
                    ($rows->sum('berhasil')/$rows->sum('total_std'))*100,
                    2
                )
                :0
        ],

        "rows"=>$rows

    ]);
}
}