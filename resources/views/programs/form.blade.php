@extends('layouts.main')
@section('title', 'Program Management')

@section('content')
    @php $isEdit = $data !== null; @endphp

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Program - {{ $isEdit ? 'Ubah' : 'Tambah' }} Data</h5>
            <small class="text-body-secondary float-end">
                <a href="{{ url('/master/programs') }}" class="btn btn-primary w-200">Kembali</a>
            </small>
        </div>
        <div class="card-body">
            @include('partials.error')

            <form method="POST" action="{{ $isEdit ? url('master/programs/' . $data->id) : url('master/programs') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Nama Program <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $data->name ?? '') }}" maxlength="100" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">URL <span class="text-danger">*</span></label>
                        <input type="text" name="url" class="form-control" value="{{ old('url', $data->url ?? '') }}"
                            maxlength="100" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <button type="submit" class="btn btn-primary w-200">Simpan</button>
                    <a href="{{ url('/master/programs') }}" class="btn btn-warning w-200">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
