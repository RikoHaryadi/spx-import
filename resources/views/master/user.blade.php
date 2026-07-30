@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-0">
                👥 Master User
            </h3>

            <small class="text-muted">
                Pengelolaan Akun Monitoring Delivery
            </small>

        </div>

        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalTambah">

            ➕ Tambah User

        </button>

    </div>
@if ($errors->any())

<div class="alert alert-danger">

    <strong>Terjadi kesalahan:</strong>

    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>

</div>

@endif
@if(session('success'))
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="alert alert-success">

{{ session('success') }}

</div>

@endif


<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table
class="table table-hover table-bordered align-middle"
id="userTable">

<thead class="table-dark">

<tr>

<th width="60">#</th>

<th>Nama</th>

<th>Email</th>

<th>NIK</th>

<th>Hub</th>

<th width="120">Role</th>

<th width="90">Status</th>

<th width="140">Action</th>

</tr>

</thead>

<tbody>

@foreach($users as $user)

<tr>

<td>

{{ $loop->iteration }}

</td>

<td>

<strong>{{ $user->name }}</strong>

</td>

<td>

{{ $user->email }}

</td>

<td>

{{ $user->nik }}

</td>

<td>

{{ optional($user->hub)->hub_name }}

</td>

<td>

@php

$color='secondary';

if($user->role=='owner') $color='danger';
elseif($user->role=='manager') $color='primary';
elseif($user->role=='spv') $color='success';
elseif($user->role=='viewer') $color='secondary';

@endphp

<span class="badge bg-{{ $color }}">

{{ strtoupper($user->role) }}

</span>

</td>

<td>

@if($user->is_active)

<span class="badge bg-success">

AKTIF

</span>

@else

<span class="badge bg-danger">

NONAKTIF

</span>

@endif

</td>

<td>

<button

class="btn btn-warning btn-sm btnEdit"

data-id="{{ $user->id }}"

data-name="{{ $user->name }}"

data-email="{{ $user->email }}"

data-nik="{{ $user->nik }}"

data-role="{{ $user->role }}"

data-hub="{{ $user->hub_id }}"

data-active="{{ $user->is_active }}">

✏ Edit

</button>

</td>

</tr>

@endforeach

</tbody>

</table>
<div class="modal fade" id="modalTambah">

<div class="modal-dialog modal-lg">

<form action="{{ route('master-user.store') }}" method="POST">

@csrf

<div class="modal-content">

<div class="modal-header bg-primary text-white">

<h5>Tambah User</h5>

<button class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="row">

<div class="col-md-6 mb-3">

<label>Nama</label>

<input
type="text"
name="name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Email SPX</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>NIK</label>

<input
type="text"
name="nik"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Hub</label>

<select
name="hub_id"
class="form-select">

@foreach($hubs as $hub)

<option value="{{ $hub->id }}">

{{ $hub->hub_name }}

</option>

@endforeach

</select>

</div>

<div class="col-md-6 mb-3">

<label>Role</label>

<select
name="role"
class="form-select">

<option value="owner">Owner</option>

<option value="manager">Manager</option>

<option value="spv">SPV</option>

<option value="viewer">Viewer</option>

</select>

</div>

</div>

</div>

<div class="modal-footer">

<button type="submit" class="btn btn-primary">
    Simpan
</button>

</div>

</div>

</form>

</div>

</div>
<div class="modal fade" id="modalEdit">

<div class="modal-dialog modal-lg">

<form
id="formEdit"
method="POST"
action="">
@csrf
@method('PUT')

<div class="modal-content">

<div class="modal-header bg-warning">

<h5>Edit User</h5>

<button
class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="modal-body">

<input type="hidden" id="editId">

<div class="row">


<div class="row">

<div class="col-md-6 mb-3">

<label>Nama</label>

<input
type="text"
id="editName"
name="name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Email</label>

<input
type="email"
id="editEmail"
name="email"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>NIK</label>

<input
type="text"
id="editNik"
name="nik"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Hub</label>

<select
id="editHub"
name="hub_id"
class="form-select">

@foreach($hubs as $hub)

<option value="{{ $hub->id }}">

{{ $hub->hub_name }}

</option>

@endforeach

</select>

</div>

<div class="col-md-6">

<label>Role</label>

<select
id="editRole"
name="role"
class="form-select">

<option value="owner">Owner</option>

<option value="manager">Manager</option>

<option value="spv">SPV</option>

<option value="viewer">Viewer</option>

</select>

</div>

<div class="col-md-6">

<label>Status</label>

<select
id="editActive"
name="is_active"
class="form-select">

<option value="1">Aktif</option>

<option value="0">Non Aktif</option>

</select>

</div>

</div>

</div>

<div class="modal-footer">

<button type="submit" class="btn btn-success">
    Update
</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</div>

<script>

document.querySelectorAll('.btnEdit').forEach(function(btn){

    btn.onclick = function(){

        let id = this.dataset.id;

        const form = document.getElementById('formEdit');

        form.action = "{{ url('master-user') }}/" + id;

        document.getElementById('editName').value = this.dataset.name;
        document.getElementById('editEmail').value = this.dataset.email;
        document.getElementById('editNik').value = this.dataset.nik;
        document.getElementById('editHub').value = this.dataset.hub;
        document.getElementById('editRole').value = this.dataset.role;
        document.getElementById('editActive').value = this.dataset.active;

        new bootstrap.Modal(
            document.getElementById('modalEdit')
        ).show();

    };

});

</script>
@endsection