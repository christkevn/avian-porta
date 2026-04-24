@extends('layouts.main')
@section('title', 'Mapping IDC Customer')

@section('content')
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header p-2">
                <button class="btn btn-primary w-100 w-200" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
                    Filter
                </button>
            </div>
            <div class="collapse" id="collapseFilter">
                <div class="p-2 border">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="success">Berhasil</option>
                                <option value="duplicate">Sudah Ada</option>
                                <option value="failed">Gagal</option>
                                <option value="error">Error</option>
                                <option value="pending">Belum Sync</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="text" id="tanggal_start" placeholder="Tanggal Awal" class="form-control mb-2">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="text" id="tanggal_end" placeholder="Tanggal Akhir" class="form-control mb-2">
                        </div>
                        <div class="col-md-6 d-flex gap-2">
                            <button id="btnFilter" class="btn btn-primary w-200">Terapkan Filter</button>
                            <button id="btnClearFilter" class="btn btn-outline-secondary w-200">Clear</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Data NOO</h5>

                <small class="text-body-secondary float-end">
                    <button type="button" class="btn btn-success w-200" data-bs-toggle="modal"
                        data-bs-target="#importModal">
                        <i class="ri ri-file-excel-2-line"></i> Import Excel
                    </button>
                    <button id="btnExport" class="btn btn-info w-200">Export Data</button>
                </small>
            </div>

            <div class="card-datatable">
                @include('partials.notification')
                @include('partials.error')
                <div class="table-responsive">
                    <table class="datatables-basic table table-bordered nowrap">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>IDC</th>
                                <th>Nama</th>
                                <th width="5%">Status</th>
                                <th>Keterangan</th>
                                <th>Tanggal proses</th>
                                <th>Alamat1</th>
                                <th>Alamat2</th>
                                <th>Kabupaten</th>
                                <th>Kecamatan</th>
                                <th>Telepon</th>
                                <th>HP</th>
                                <th>Longitude</th>
                                <th>Latitude</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('data_noo.partial.import')
    @include('data_noo.partial.preview_image')
    @include('partials.delete')
@endsection

@section('css')
    <style>
        table td,
        table th {
            padding-left: 16px !important;
        }

        #btnFilter,
        #btnClearFilter {
            height: 38px !important;
            margin-top: 24px !important;
        }
    </style>
@endsection

@section('script')
    @include('partials.deletejs')
    @include('data_noo.partial.preview_js')
    <script>
        const dateConfig = {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y",
            allowInput: true
        };
        const tanggal_start = flatpickr("#tanggal_start", dateConfig);
        const tanggal_end = flatpickr("#tanggal_end", dateConfig);

        let table = $('.datatables-basic').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            ajax: {
                url: "{{ route('data-noo.datatable') }}",
                data: function(d) {
                    d.sync_status = $('#filterStatus').val();
                    d.tanggal_start = $('#tanggal_start').val();
                    d.tanggal_end = $('#tanggal_end').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'IDC',
                    name: 'IDC'
                },
                {
                    data: 'Nama',
                    name: 'Nama'
                },
                {
                    data: 'sync_status',
                    name: 'sync_status',
                    render: function(data) {
                        const map = {
                            success: ['success', 'Berhasil'],
                            duplicate: ['warning text-dark', 'Sudah Ada'],
                            failed: ['danger', 'Gagal'],
                            error: ['danger', 'Error'],
                            pending: ['secondary', 'Belum Sync']
                        };

                        if (!data || !map[data]) {
                            return '<span class="badge bg-secondary">Belum Sync</span>';
                        }

                        return `<span class="badge bg-${map[data][0]}">${map[data][1]}</span>`;
                    }
                },
                {
                    data: 'keterangan',
                    name: 'keterangan',
                    orderable: false,
                    render: function(data) {
                        return data ? `<span class="text-muted small">${data}</span>` : '';
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'Alamat1',
                    name: 'Alamat1'
                },
                {
                    data: 'Alamat2',
                    name: 'Alamat2'
                },
                {
                    data: 'Kabupaten',
                    name: 'Kabupaten'
                },
                {
                    data: 'Kecamatan',
                    name: 'Kecamatan'
                },
                {
                    data: 'Telepon',
                    name: 'Telepon',
                    orderable: false,
                    render: function(data) {
                        if (!data) return '';
                        return data.split(';')
                            .map(i => i.trim())
                            .filter(i => i !== '')
                            .join('<br>');
                    }
                },
                {
                    data: 'HP',
                    name: 'HP',
                    orderable: false,
                    render: function(data) {
                        if (!data) return '';
                        return data.split(';')
                            .map(i => i.trim())
                            .filter(i => i !== '')
                            .join('<br>');
                    }
                },
                {
                    data: 'Longitude',
                    name: 'Longitude'
                },
                {
                    data: 'Latitude',
                    name: 'Latitude'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [1, 'asc']
            ]
        });


        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });

        $('#btnClearFilter').on('click', function() {
            $('#filterStatus').val('');
            tanggal_start.clear();
            tanggal_end.clear();

            table.ajax.reload();
        });

        $('#btnExport').on('click', function() {
            const syncStatus = $('#filterStatus').val();
            const url = new URL("{{ route('data-noo.export') }}", window.location.origin);
            if (syncStatus) {
                url.searchParams.append('sync_status', syncStatus);
            }
            window.location.href = url.toString();
        });

        $(document).on('click', '.btn-retry', function() {
            const url = $(this).data('url');

            Swal.fire({
                title: 'Retry Sync?',
                text: 'Data akan di-sync ulang ke Tirta.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Retry',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (!result.isConfirmed) return;

                $.post(url, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(res => {
                        Swal.fire('Berhasil', res.message, 'success');
                        table.ajax.reload(null, false);
                    })
                    .fail(err => {
                        Swal.fire('Gagal', err.responseJSON?.message ?? 'Retry gagal', 'error');
                    });
            });
        });
    </script>
@endsection
