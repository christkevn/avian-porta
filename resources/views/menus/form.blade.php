@extends('layouts.main')
@section('title', 'Menu Management')

@section('content')
    @php $isEdit = $data !== null; @endphp

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Menu - {{ $isEdit ? 'Ubah' : 'Tambah' }} Data</h5>
            <small class="text-body-secondary float-end">
                <a href="{{ url('/master/menus') }}" class="btn btn-primary w-200">Kembali</a>
            </small>
        </div>
        <div class="card-body">
            @include('partials.error')

            <form method="POST" action="{{ $isEdit ? url('master/menus/' . $data->id) : url('master/menus') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Program <span class="text-danger">*</span></label>
                        <select name="program_id" class="form-select" required>
                            <option value="">-- Pilih Program --</option>
                            @foreach ($programs as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('program_id', $data->program_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $data->name ?? '') }}" maxlength="100" required>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <div class="d-flex justify-content-end flex-wrap flex-sm-nowrap gap-2">
                            <button type="submit" class="btn btn-primary w-100 w-200">Simpan</button>
                            <a href="{{ url('/master/menus') }}" type="button"
                                class="btn btn-warning w-100 w-200">Batal</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
