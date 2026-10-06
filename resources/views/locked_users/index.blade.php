@extends('layouts.main')

@section('title', 'User Terkunci')

@section('content')
    <div class="col-xxl">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Terkunci (24 jam terakhir)</h5>
                <small class="text-body-secondary">Unlock = reset percobaan login + user wajib ganti password saat login
                    berikutnya.</small>
            </div>
            <div class="card-datatable text-nowrap">
                <div class="table-responsive">
                    <table class="datatables-basic table table-bordered dataTable">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>IP Terakhir</th>
                                <th>Percobaan</th>
                                <th>Percobaan Terakhir</th>
                                <th>Kunci Sampai</th>
                                <th>Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const table = $('.dataTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('master.locked-users.datatable') }}",
            columns: [{
                    data: 'username',
                    name: 'login_attempts.username'
                },
                {
                    data: 'nama',
                    name: 'users.nama'
                },
                {
                    data: 'ip_address',
                    name: 'login_attempts.ip_address'
                },
                {
                    data: 'attempt_count',
                    name: 'login_attempts.attempt_count'
                },
                {
                    data: 'last_attempt_at',
                    name: 'login_attempts.last_attempt_at'
                },
                {
                    data: 'locked_until',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            order: [
                [4, 'desc']
            ]
        });

        function postAction(url, username) {
            return $.post(url, {
                _token: "{{ csrf_token() }}",
                username
            });
        }

        $(document).on('click', '.btn-unlock', function() {
            const username = $(this).data('username');

            Swal.fire({
                title: `Unlock ${username}?`,
                text: 'Percobaan login di-reset dan user wajib ganti password saat login berikutnya.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, unlock',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: () => postAction("{{ route('master.locked-users.unlock') }}", username)
                    .catch(() => Swal.showValidationMessage('Gagal unlock user.'))
            }).then(r => {
                if (!r.isConfirmed) return;
                table.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: `${username} sudah di-unlock.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });

        $(document).on('click', '.btn-reset', function() {
            const username = $(this).data('username');

            Swal.fire({
                title: `Reset password ${username}?`,
                text: 'Password lama tidak bisa dipakai lagi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, reset',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: () => postAction("{{ route('master.locked-users.reset-password') }}", username)
                    .catch(() => Swal.showValidationMessage('Gagal reset password.'))
            }).then(r => {
                if (!r.isConfirmed) return;
                table.ajax.reload();

                Swal.fire({
                    icon: 'success',
                    title: 'Password direset',
                    html: `Password sementara untuk <b>${username}</b>:<br><br>
                           <input id="tempPass" class="swal2-input" value="${r.value.temp_password}" readonly>
                           <small class="text-muted">Salin sekarang, tidak akan ditampilkan lagi.</small>`,
                    confirmButtonText: 'Salin & Tutup',
                    allowOutsideClick: false,
                    preConfirm: () => navigator.clipboard?.writeText(r.value.temp_password).catch(
                        () => {})
                });
            });
        });
    </script>
@endsection
