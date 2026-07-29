@extends('layouts.app')

@section('content')
<div class="card mb-3">

    <div class="card-body">

        <form method="GET"
              action="{{ route('dashboard') }}">

            <div class="row">

                <div class="col-md-3">

                    <input type="date"
                           name="tanggal"
                           value="{{ request('tanggal') }}"
                           class="form-control">

                </div>

                <div class="col-md-2">

                    <button type="submit"
                            class="btn btn-primary w-100">

                        Filter

                    </button>

                </div>

                <div class="col-md-2">

                    <a href="{{ route('dashboard') }}"
                       class="btn btn-secondary w-100">

                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>
<div class="container-fluid mt-3">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

    <div class="d-flex justify-content-between align-items-center">

        <h4 class="mb-0">
            📊 Dashboard STD SPX
        </h4>

        <div class="text-end">

            <div class="fw-bold">

                <span id="liveIndicator"
                      class="badge bg-success">

                    🟢 LIVE

                </span>

            </div>

            <small>

                Update terakhir :

                <span id="lastUpdate">

                    {{ now()->format('H:i:s') }}

                </span>

            </small>

        </div>

    </div>

</div>

        <div class="card-body">
            <div class="row mb-3">

    <div class="col-md-3">

        <div class="card bg-primary text-white">

            <div class="card-body">

                <h5>Total STD</h5>

                <h3 id="grandTotal">{{ number_format($grand_total_std ?? 0) }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card bg-success text-white">

            <div class="card-body">

                <h5>Berhasil</h5>

                <h3 id="grandBerhasil">{{ number_format($grand_berhasil ?? 0) }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card bg-danger text-white">

            <div class="card-body">

                <h5>Tidak Berhasil</h5>

                <h3 id="grandGagal">{{ number_format($grand_tidak_berhasil ?? 0) }}</h3>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card bg-warning">

            <div class="card-body">

                <h5>% STD</h5>

                <h3 id="grandPersen">{{ $grand_persen ?? 0 }}%</h3>

            </div>

        </div>

    </div>

</div>


            <table class="table table-bordered table-striped table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>Tanggal</th>
                        <th>Hub</th>
                        <th>Total STD</th>
                        <th>Berhasil</th>
                        <th>Tidak Berhasil</th>
                        <th>% STD</th>

                    </tr>

                </thead>

                <tbody id="dashboardTable">

                @forelse($data as $row)

                    <tr>

                        <td>
                            {{ $row->date_id }}
                        </td>

                        <td>
                            {{ $row->lmhub_station_name }}
                        </td>

                        <td>
                            {{ number_format($row->total_std) }}
                        </td>

                        <td>
                            {{ number_format($row->berhasil) }}
                        </td>

                       <td>

    <a href="{{ route('dashboard.detail', [
        'date' => $row->date_id,
        'hub' => $row->lmhub_station_name
    ]) }}"
    class="btn btn-sm btn-danger">

        {{ number_format($row->tidak_berhasil) }}

    </a>

</td>

                        <td>

                            @if($row->persentase >= 95)

                                <span class="badge bg-success">
                                    {{ $row->persentase }} %
                                </span>

                            @elseif($row->persentase >= 90)

                                <span class="badge bg-warning text-dark">
                                    {{ $row->persentase }} %
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    {{ $row->persentase }} %
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center">

                            Belum ada data

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
<script>
setInterval(refreshDashboard,15000);

function refreshDashboard(){

    let tanggal =
        document.querySelector('[name=tanggal]').value;

    fetch("/dashboard/live?tanggal="+tanggal)

    .then(r=>r.json())

    .then(data=>{

            document.getElementById("grandTotal").innerHTML =
                Number(data.summary.total).toLocaleString();

            document.getElementById("grandBerhasil").innerHTML =
                Number(data.summary.berhasil).toLocaleString();

            document.getElementById("grandGagal").innerHTML =
                Number(data.summary.gagal).toLocaleString();

            document.getElementById("grandPersen").innerHTML =
                data.summary.persen+"%";
                document.getElementById("lastUpdate").innerHTML =
                new Date().toLocaleTimeString('id-ID');
            document.getElementById("liveIndicator").innerHTML =
                "🟡 Updating...";

            document.getElementById("liveIndicator").innerHTML =
                "🟢 LIVE";

                document.getElementById("liveIndicator")
                .className="badge bg-success";

                .catch(function(){

    document.getElementById("liveIndicator").innerHTML =
    "🔴 Offline";

    document.getElementById("liveIndicator")
    .className="badge bg-danger";

});

            let html='';

            data.rows.forEach(function(r){

                let badge='bg-danger';

                if(r.persentase>=95)
                    badge='bg-success';
                else if(r.persentase>=90)
                    badge='bg-warning text-dark';

                html+=`
    <tr>

    <td>${r.date_id}</td>

    <td>${r.hub}</td>

    <td>${Number(r.total_std).toLocaleString()}</td>

    <td>${Number(r.berhasil).toLocaleString()}</td>

    <td>
<a href="/dashboard/detail?date=${encodeURIComponent(r.date_id)}&hub=${encodeURIComponent(r.hub)}"
class="btn btn-sm btn-danger">

${Number(r.tidak_berhasil).toLocaleString()}

</a>
</td>

<td>

<span class="badge ${badge}">

${r.persentase} %

</span>

</td>

</tr>
`;

        });

        document.getElementById("dashboardTable").innerHTML=html;

    });

}
</script>
@endsection