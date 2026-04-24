@extends('layouts.main')
@section('title', 'Mapping IDC Customer')

@section('content')
    @php $isEdit = $data !== null; @endphp

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Mapping IDC Customer - {{ $isEdit ? 'Ubah' : 'Tambah' }} Data</h5>
            <small class="text-body-secondary float-end">
                <a href="{{ url('/admin/data-noo') }}" class="btn btn-primary w-100 w-200">Kembali</a>
            </small>
        </div>

        <div class="card-body">
            @include('partials.error')

            <form method="POST" action="{{ $isEdit ? url('admin/data-noo/' . $data->id) : url('admin/data-noo') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label">IDC <span class="text-danger">*</span></label>
                        <input type="text" name="IDC" class="form-control" value="{{ old('IDC', $data->IDC ?? '') }}"
                            maxlength="20">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="Nama" class="form-control"
                            value="{{ old('Nama', $data->Nama ?? '') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label">NIK</label>
                        <input type="text" name="NIK" class="form-control"
                            value="{{ old('NIK', $data->NIK ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Alamat 1</label>
                        <textarea name="Alamat1" class="form-control" rows="2">{{ old('Alamat1', $data->Alamat1 ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Alamat 2</label>
                        <textarea name="Alamat2" class="form-control" rows="2">{{ old('Alamat2', $data->Alamat2 ?? '') }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label">Kabupaten <span class="text-danger">*</span></label>
                        <input type="text" name="Kabupaten" class="form-control"
                            value="{{ old('Kabupaten', $data->Kabupaten ?? '') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label">Kecamatan <span class="text-danger">*</span></label>
                        <input type="text" name="Kecamatan" class="form-control"
                            value="{{ old('Kecamatan', $data->Kecamatan ?? '') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label">Status</label>
                        <input type="text" name="Status" class="form-control"
                            value="{{ old('Status', $data->Status ?? '') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label">Telepon</label>
                        <input type="text" name="Telepon" class="form-control"
                            value="{{ old('Telepon', $data->Telepon ?? '') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label">HP</label>
                        <input type="text" name="HP" class="form-control" value="{{ old('HP', $data->HP ?? '') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label">Longitude <span class="text-danger">*</span></label>
                        <input type="text" name="Longitude" class="form-control"
                            value="{{ old('Longitude', $data->Longitude ?? '') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label">Latitude <span class="text-danger">*</span></label>
                        <input type="text" name="Latitude" class="form-control"
                            value="{{ old('Latitude', $data->Latitude ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Image Toko Avian (URL)</label>
                        <input type="text" name="ImageTokoAvian" class="form-control"
                            value="{{ old('ImageTokoAvian', $data->ImageTokoAvian ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Image Toko Avian 2 (URL)</label>
                        <input type="text" name="ImageTokoAvian1" class="form-control"
                            value="{{ old('ImageTokoAvian1', $data->ImageTokoAvian1 ?? '') }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="col-form-label">Keterangan Avian</label>
                        <textarea name="KeteranganAvian" class="form-control" rows="3">{{ old('KeteranganAvian', $data->KeteranganAvian ?? '') }}</textarea>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary w-100 w-200">Simpan</button>
                            <a href="{{ url('/admin/data-noo') }}" class="btn btn-warning w-100 w-200">Batal</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
