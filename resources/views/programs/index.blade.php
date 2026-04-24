@extends('layouts.main')
@section('title', 'Program Management')

@section('content')
    <div class="col-xxl">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Manajemen Program</h5>
                <small class="text-body-secondary float-end">
                    <a href="{{ url('/master/programs/create') }}" class="btn btn-primary w-200">
                        <i class="ri ri-add-line"></i> Tambah Program
                    </a>
                </small>
            </div>
            <div class="card-datatable">
                @include('partials.notification')
                @include('partials.error')
                <div class="table-responsive p-2">
                    <table class="datatables-basic table table-bordered nowrap">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Program</th>
                                <th>URL</th>
                                <th>Dibuat</th>
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
        $(document).ready(function() {
            $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('/master/programs/datatable') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'url',
                        name: 'url'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });
    </script>
@endsection
