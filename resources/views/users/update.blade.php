@extends('layouts.main')

@section('title', 'User')

@section('content')
    @php
        $isEdit = $data !== null;
    @endphp

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>User - {{ $isEdit ? 'Ubah' : 'Tambah' }} Data</h5>
            <small class="text-body-secondary float-end">
                <a href="{{ url('/master/users') }}" class="btn btn-primary w-200">Kembali</a>
            </small>
        </div>

        <div class="card-body">
            @include('partials.error')

            <form method="POST" action="{{ $isEdit ? url('master/users/' . $data->id) : url('master/users') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Username</label>
                        <input type="text" name="username" class="form-control"
                            value="{{ old('username', $data->username ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">
                            Password {{ $isEdit ? '(Biarkan kosong jika tidak diubah)' : '' }}
                        </label>

                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="passwordField">
                            <span class="input-group-text cursor-pointer" id="togglePassword">
                                <i class="ri ri-eye-off-line icon-20px"></i>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Tipe User</label>
                        {{ Form::select('tipe_user', ['AD' => 'ADMIN', 'USER' => 'USER'], old('tipe', $data->tipe ?? ''), [
                            'class' => 'form-select',
                        ]) }}
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Nama</label>
                        <input type="text" name="nama_users" class="form-control"
                            value="{{ old('nama_users', $data->nama ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Email</label>
                        <input type="text" name="email_users" class="form-control"
                            value="{{ old('email_users', $data->email ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Level</label>
                        <input type="text" name="level_users" class="form-control"
                            value="{{ old('level_users', $data->level ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Cabang</label>
                        <input type="text" name="cabang_users" class="form-control"
                            value="{{ old('cabang_users', $data->cabang ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Status</label>
                        {{ Form::select('aktif', ['1' => 'Aktif', '0' => 'Nonaktif'], old('aktif', $data->aktif ?? '1'), [
                            'class' => 'form-select',
                        ]) }}
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <div class="d-flex justify-content-end flex-wrap flex-sm-nowrap gap-2">
                            <button type="submit" class="btn btn-primary w-100 w-200">Simpan</button>
                            <a href="<?= url('/master/users') ?>" type="button"
                                class="btn btn-warning w-100 w-200">Batal</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('passwordField');
            const toggleBtn = document.getElementById('togglePassword');
            const icon = toggleBtn.querySelector('i');

            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';

                icon.classList.toggle('ri-eye-off-line');
                icon.classList.toggle('ri-eye-line');
            });
        });
    </script>
@endsection
