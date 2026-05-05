<!-- LAYOUT -->
@extends('layouts.main')

<!-- TITLE -->
@section('title', 'User')

<!-- CONTENT -->
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
                                <input type="text" id="filter_name" class="form-control" placeholder="Cari Nama"
                                    value="{{ session('user_filter.name') }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <select id="filter_level" class="form-select">
                                    <option value="">Semua Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->level }}"
                                            {{ session('user_filter.level') == $level->level ? 'selected' : '' }}>
                                            {{ $level->level }}
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
                <h5 class="mb-0">User</h5>
                <small class="text-body-secondary float-end">
                    <a href="<?= url('/master/users/create') ?>" class="btn btn-primary w-200">Tambah User</a>
                </small>
            </div>
            <div class="card-datatable text-nowrap">
                @include('partials.notification')

                <div class="table-responsive">
                    <table class="datatables-basic table table-bordered dataTable">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Tipe</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th width=5%>Status</th>
                                <th width=10%>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('partials.delete')
@endsection

<!-- CSS -->
@section('css')
@endsection

<!-- JAVASCRIPT -->
@section('script')
    @include('partials.deletejs')
    <script>
        let table = $('.dataTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('master.users.datatable') }}",
                data: function(d) {
                    d.level = $('#filter_level').val();
                    d.name = $('#filter_name').val();
                }
            },

            columns: [{
                    data: 'username',
                    name: 'username',
                },
                {
                    data: 'tipe',
                    name: 'tipe',
                },
                {
                    data: 'nama',
                    name: 'nama',
                },
                {
                    data: 'email',
                    name: 'email',
                },
                {
                    data: 'level',
                    name: 'level',
                },
                {
                    data: 'aktif',
                    render: function(data) {
                        var text = data == 1 ? "Aktif" : "Nonaktif";
                        var label = data == 1 ? "primary" : "warning";
                        return "<span class='badge rounded-pill bg-label-" + label + "'>" + text +
                            "</span>";
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                }
            ],

            order: [
                [0, 'desc']
            ]
        });


        $('#btnFilter').on('click', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('#btnClearFilter').on('click', function(e) {
            e.preventDefault();

            $.post("{{ route('master.users.clearFilter') }}", {
                _token: "{{ csrf_token() }}"
            }, function() {
                $('#filter_level').val('');
                $('#filter_profesi').val('');
                $('#filter_name').val('');
                table.ajax.reload();
            });
        });
    </script>

@endsection
