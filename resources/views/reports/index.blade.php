@extends('layouts.main')
@section('title', 'Laporan Aktivitas')

@section('css')
    <style>
        #report-table thead th,
        #detail-body thead th {
            text-align: left !important;
        }
    </style>
@endsection

@section('content')
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('master.reports.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Program</label>
                        <select name="program_id" class="form-select">
                            <option value="">Semua Program</option>
                            @foreach ($programs as $p)
                                <option value="{{ $p->id }}" @selected($programId == $p->id)>{{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Dari</label>
                        <input type="date" name="from" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai</label>
                        <input type="date" name="to" value="{{ $to }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100"><i class="ri ri-filter-3-line"></i> Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($errors as $error)
            <div class="alert alert-warning">{{ $error }}</div>
        @endforeach

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <small class="text-body-secondary">Total Aktivitas</small>
                        <h3 class="mb-0">{{ number_format($totalAll) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <small class="text-body-secondary">Menu Terpakai</small>
                        <h3 class="mb-0">{{ number_format($rows->count()) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Aktivitas per Menu</h5>
            </div>
            <div class="table-responsive p-2">
                <table class="table table-bordered nowrap" id="report-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Program</th>
                            <th>Menu</th>
                            <th>Total Aktivitas</th>
                            <th>Terakhir</th>
                            <th width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->program_name }}</td>
                                <td>{{ $row->menu ?: '-' }}</td>
                                <td class="text-end">{{ number_format($row->total) }}</td>
                                <td data-order="{{ $row->terakhir }}">{{ formatDate($row->terakhir, 'd M Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail" data-program="{{ $row->program_id }}"
                                        data-name="{{ $row->program_name }}" data-menu="{{ $row->menu }}">
                                        <i class="ri ri-eye-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detail-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detail-body"></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#report-table').DataTable({
                order: [
                    [3, 'desc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [0, 5]
                }]
            });

            const esc = s => $('<div>').text(s ?? '-').html();
            const range = {
                from: '{{ $from }}',
                to: '{{ $to }}'
            };

            $(document).on('click', '.btn-detail', function() {
                const b = $(this);
                $('#detail-title').text(b.data('name') + ' / ' + (b.data('menu') || '-'));
                $('#detail-body').html('<div class="text-center p-4">Memuat...</div>');
                $('#detailModal').modal('show');

                $.getJSON('{{ route('master.reports.detail') }}', {
                    program_id: b.data('program'),
                    menu: b.data('menu'),
                    ...range
                }).done(function(res) {
                    const actions = res.actions.map(a =>
                        `<tr><td>${esc(a.log_name)}</td><td class="text-end">${a.total}</td><td>${esc(a.terakhir)}</td></tr>`
                    ).join('');

                    $('#detail-body').html(`
                        <table class="table table-sm table-bordered">
                            <thead><tr><th>Aksi</th><th>Jumlah</th><th>Terakhir</th></tr></thead>
                            <tbody>${actions}</tbody>
                        </table>`);
                }).fail(function() {
                    $('#detail-body').html(
                        '<div class="alert alert-danger">Gagal memuat detail.</div>');
                });
            });
        });
    </script>
@endsection
