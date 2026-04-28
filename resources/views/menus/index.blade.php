@extends('layouts.main')
@section('title', 'Menu Management')

@section('content')
    <div class="col-12 mb-5">
        <div class="card">
            <div class="card-header" style="padding: 2">
                <button class="btn btn-primary me-1 w-100 w-200" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Filter</button>
                <div class="collapse" id="collapseExample">
                    <div class="p-2 border">
                        <div class="row g-3">

                            <div class="col-md-3 d-flex gap-2">
                                <select id="filter_program" class="form-select">
                                    <option value="">Semua Program</option>
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->id }}"
                                            {{ session('menu_filter.program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button id="btnFilter" class="btn btn-primary w-50">Terapkan Filter</button>
                                <button id="btnClearFilter" class="btn btn-outline-secondary w-50">Clear</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Manajemen Menu</h5>
                <small class="text-body-secondary float-end">
                    <a href="{{ url('/master/menus/create') }}" class="btn btn-primary w-200">
                        <i class="ri ri-add-line"></i> Tambah Menu
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
        $(document).ready(function() {
            $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('master.menus.datatable') }}",
                    data: function(d) {
                        d.program_id = $('#filter_program').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'program_name',
                        name: 'program_name'
                    },
                    {
                        data: 'name',
                        name: 'name'
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

        $('#btnFilter').on('click', function(e) {
            e.preventDefault();
            var programId = $('#filter_program').val();
            window.location.href = "{{ url('/master/menus') }}?program_id=" + programId;
        });

        $('#btnClearFilter').on('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ url('/master/menus') }}";
        });
    </script>
@endsection
