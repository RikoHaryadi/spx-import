@extends('layouts.app')

@section('content')
<div class="container">

    <h4 class="mb-3">Performance Kurir</h4>

    {{-- FILTER --}}
    <form method="GET" action="{{ url('/performance/kurir') }}" class="row g-3 mb-4">

        <div class="col-md-3">
            <label>Tanggal</label>
            <input type="date" name="date" class="form-control"
                   value="{{ request('date') }}" required>
        </div>

        <div class="col-md-3">
            <label>Hub</label>
          <select name="hub" class="form-control" required>
    <option value="">-- Pilih Hub --</option>

    @foreach($hubs as $h)
        <option value="{{ $h->lmhub_station_name }}"
            {{ request('hub') == $h->lmhub_station_name ? 'selected' : '' }}>
            {{ $h->lmhub_station_name }}
        </option>
    @endforeach

</select>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary">Tampilkan</button>
        </div>

    </form>

    {{-- TABLE --}}
    @if($data && count($data) > 0)

        <div class="card">
            <div class="card-body table-responsive">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Driver ID</th>
                            <th>Driver Name</th>
                            <th>Total STD</th>
                            <th>Berhasil</th>
                            <th>Tidak Berhasil</th>
                            <th>%</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($data as $row)
                            <tr>
                                <td>{{ $row->driver_id }}</td>
                                <td>{{ $row->driver_name }}</td>
                                <td>{{ $row->total_std }}</td>
                                <td>{{ $row->berhasil }}</td>
                                <td>{{ $row->tidak_berhasil }}</td>
                                <td>
                                    <span class="badge bg-{{ $row->persentase >= 90 ? 'success' : 'warning' }}">
                                        {{ $row->persentase }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>

    @elseif(request('date') && request('hub'))
        <div class="alert alert-warning">
            Data tidak ditemukan
        </div>
    @endif

</div>
@endsection