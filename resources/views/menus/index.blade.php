@extends('layouts.main')
@section('title', 'Menu Management')

@section('content')
<div class="col-xxl">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Manajemen Menu</h5>
            <a href="{{ url('/master/menus/create') }}" class="btn btn-primary w-200">
                <i class="ri ri-add-line"></i> Tambah Menu
            </a>
        </div>
        <div class="card-datatable">
            @include('partials.notification')
            @include('partials.error')
            <div class="table-responsive p-2">
                <table class="datatables-basic table table-bordered nowrap">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Program</th>
                            <th>Nama Menu</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@include('partials.delete')
@endsection

@section('script')
@include('partials.deletejs')
<script>
$(document).ready(function () {
    $('.datatables-basic').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url("/master/menus/datatable") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'program_name', name: 'program_name' },
            { data: 'name', name: 'name' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });
});
</script>
@endsection
