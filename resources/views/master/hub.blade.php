@extends('layouts.app')

@section('content')

<div class="container-fluid">

<h3 class="mb-4">

🏢 Master Hub SPX

</h3>

@if(session('success'))

<div class="alert alert-success">

{{ session('success') }}

</div>

@endif

<div class="row mb-4">

<div class="col-lg-3">

<div class="card border-primary shadow-sm">

<div class="card-body text-center">

<h6>Total Hub</h6>

<h2 class="text-primary">

{{ $summary['totalHub'] }}

</h2>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="card border-success shadow-sm">

<div class="card-body text-center">

<h6>Hub Aktif</h6>

<h2 class="text-success">

{{ $summary['activeHub'] }}

</h2>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="card border-warning shadow-sm">

<div class="card-body text-center">

<h6>Region</h6>

<h2 class="text-warning">

{{ $summary['region'] }}

</h2>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="card border-info shadow-sm">

<div class="card-body text-center">

<h6>Total User</h6>

<h2 class="text-info">

{{ $summary['user'] }}

</h2>

</div>

</div>

</div>

</div>

<div class="card shadow">

<div class="card-header bg-dark text-white">

<div class="row">

<div class="col-md-6">

<input

type="text"

id="searchHub"

class="form-control"

placeholder="🔍 Cari Hub...">

</div>

<div class="col-md-6 text-end">

<button
class="btn btn-success"
data-bs-toggle="modal"
data-bs-target="#modalHub">

➕ Tambah Hub

</button>

</div>

</div>

</div>

<div class="card-body table-responsive">

<table class="table table-hover align-middle"

id="hubTable">

<thead class="table-dark">

<tr>

<th>Kode</th>

<th>Nama Hub</th>

<th>Kota</th>

<th>Region</th>

<th>Status</th>

<th width="140">

Action

</th>

</tr>

</thead>

<tbody>

@foreach($hubs as $hub)

<tr>

<td>

<strong>

{{ $hub->hub_code }}

</strong>

</td>

<td>

{{ $hub->hub_name }}

</td>

<td>

{{ $hub->city }}

</td>

<td>

{{ $hub->region }}

</td>

<td>

@if($hub->is_active)

<span class="badge bg-success">

Active

</span>

@else

<span class="badge bg-danger">

Non Active

</span>

@endif

</td>

<td>

<button
class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#editHub{{ $hub->id }}">

✏

</button>

<form
action="{{ route('hub.destroy',$hub) }}"
method="POST"
class="d-inline">

@csrf
@method('DELETE')

<button
onclick="return confirm('Hapus Hub ini?')"
class="btn btn-danger btn-sm">

🗑

</button>

</form>

</td>

</tr>
<!-- Modal Edit -->

<div class="modal fade"

id="editHub{{ $hub->id }}"

tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<form
action="{{ route('hub.update',$hub) }}"
method="POST">

@csrf
@method('PUT')

<div class="modal-header bg-warning">

<h5>Edit Hub</h5>

<button
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="mb-3">

<label>Kode Hub</label>

<input
type="text"
name="hub_code"
class="form-control"
value="{{ $hub->hub_code }}"
required>

</div>

<div class="mb-3">

<label>Nama Hub</label>

<input
type="text"
name="hub_name"
class="form-control"
value="{{ $hub->hub_name }}"
required>

</div>

<div class="mb-3">

<label>Kota</label>

<input
type="text"
name="city"
class="form-control"
value="{{ $hub->city }}">

</div>

<div class="mb-3">

<label>Region</label>

<input
type="text"
name="region"
class="form-control"
value="{{ $hub->region }}">

</div>

<div class="form-check">

<input
type="checkbox"
name="is_active"
value="1"
class="form-check-input"
{{ $hub->is_active ? 'checked' : '' }}>

<label class="form-check-label">

Hub Aktif

</label>

</div>

</div>

<div class="modal-footer">

<button
type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Batal

</button>

<button
class="btn btn-warning">

💾 Update Hub

</button>

</div>

</form>

</div>

</div>

</div>

@endforeach

</tbody>

</table>

{{ $hubs->links() }}

</div>

</div>

</div>
<!-- Modal Tambah Hub -->

<div class="modal fade"

id="modalHub"

tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<form

action="{{ route('hub.store') }}"

method="POST">

@csrf

<div class="modal-header bg-success text-white">

<h5>

Tambah Hub SPX

</h5>

<button

class="btn-close"

data-bs-dismiss="modal">

</button>

</div>

<div class="modal-body">

<div class="mb-3">

<label>

Kode Hub

</label>

<input

type="text"

name="hub_code"

class="form-control"

required>

</div>

<div class="mb-3">

<label>

Nama Hub

</label>

<input

type="text"

name="hub_name"

class="form-control"

required>

</div>

<div class="mb-3">

<label>

Kota

</label>

<input

type="text"

name="city"

class="form-control">

</div>

<div class="mb-3">

<label>

Region

</label>

<input

type="text"

name="region"

class="form-control">

</div>

<div class="form-check">

<input

class="form-check-input"

type="checkbox"

checked

name="is_active"

value="1">

<label>

Hub Aktif

</label>

</div>

</div>

<div class="modal-footer">

<button

class="btn btn-secondary"

data-bs-dismiss="modal"

type="button">

Batal

</button>

<button

class="btn btn-success">

💾 Simpan Hub

</button>

</div>

</form>

</div>

</div>

</div>
<script>

document.getElementById('searchHub')

.addEventListener('keyup',function(){

let value=this.value.toLowerCase();

document.querySelectorAll('#hubTable tbody tr')

.forEach(function(row){

row.style.display=

row.innerText.toLowerCase().includes(value)

?''

:'none';

});

});

</script>

@endsection