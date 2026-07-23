<!DOCTYPE html>
<html>
<head>

    <title>Detail Tidak Berhasil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container-fluid mt-3">

    <div class="card shadow">

        <div class="card-header bg-danger text-white">

            <h4>

                Detail Tidak Berhasil

            </h4>

            <small>

                {{ $date }} |
                {{ $hub }}

            </small>

        </div>

        <div class="card-body">

            <a href="/dashboard"
               class="btn btn-secondary mb-3">

                Kembali

            </a>

            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover">

                    <thead class="table-dark">

                        <tr>

                            <th>Shipment ID</th>
                            <th>Driver ID</th>
                            <th>Driver Name</th>
                            <th>Status</th>
                            <th>On Hold Reason</th>
                            <th>Payment</th>
                            <th>Order Account</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($data as $row)

                        <tr>

                            <td>{{ $row->shipment_id }}</td>

                            <td>{{ $row->driver_id }}</td>

                            <td>{{ $row->driver_name }}</td>

                            <td>{{ $row->status }}</td>

                           <td>{{ $row->display_reason }}</td>

                            <td>{{ $row->payment_method }}</td>

                            <td>{{ $row->order_account }}</td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="text-center">

                                Tidak ada data

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            {{ $data->links('pagination::bootstrap-5') }}

        </div>

    </div>

</div>

</body>
</html>