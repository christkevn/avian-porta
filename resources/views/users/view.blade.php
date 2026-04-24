@extends('layouts.main')

@section('title', 'User')

@section('content')
    @php
        $user = is_array($data) || $data instanceof \Illuminate\Support\Collection ? $data[0] : $data;

    @endphp

    <div class="col-xxl">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">User - Lihat Data</h5>
                <small class="text-body-secondary float-end">
                    <a href="{{ url('/master/users') }}" class="btn btn-primary w-200">Kembali</a>
                </small>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Username</label>
                        <input disabled type="text" class="form-control" value="{{ $user->username }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Tipe User</label>
                        <input disabled type="text" class="form-control"
                            value="{{ $user->tipe == 'AD' ? 'ADMIN' : 'USER' }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Nama</label>
                        <input disabled type="text" class="form-control" value="{{ $user->nama }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Email</label>
                        <input disabled type="text" class="form-control" value="{{ $user->email }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Level</label>
                        <input disabled type="text" class="form-control" value="{{ $user->level }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label" for="status">Status</label>
                        <div class="d-flex align-items-center">
                            <?= status($user->aktif) ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
