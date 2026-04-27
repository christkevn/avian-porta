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

            <form method="POST" action="{{ $isEdit ? url('master/programs/' . $data->id) : url('master/programs') }}"
                enctype="multipart/form-data">
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

                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Foto Program</label>
                        <input type="file" name="photo" class="form-control" accept="image/*"
                            onchange="previewImage(this)">
                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</small>

                        @if ($isEdit && $data->photo_url)
                            <div class="mt-2" id="current-photo">
                                <label class="form-label">Foto Saat Ini:</label>
                                <div>
                                    <img src="{{ $data->photo_url }}" alt="Current Photo"
                                        style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                                    <button type="button" class="btn btn-sm btn-danger mt-1" onclick="removePhoto()">
                                        <i class="ri ri-delete-bin-line"></i> Hapus Foto
                                    </button>
                                </div>
                                <input type="hidden" name="remove_photo" id="remove_photo" value="0">
                            </div>
                        @endif
                        <div id="photo-preview" class="mt-2" style="display: none;">
                            <label class="form-label">Preview Foto Baru:</label>
                            <div>
                                <img id="preview" src="#" alt="Preview"
                                    style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                            </div>
                        </div>
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

@section('script')
    <script>
        function previewImage(input) {
            const previewDiv = document.getElementById('photo-preview');
            const preview = document.getElementById('preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewDiv.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                previewDiv.style.display = 'none';
            }
        }

        function removePhoto() {
            Swal.fire({
                title: 'Yakin?',
                text: 'Foto akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('remove_photo').value = '1';
                    document.getElementById('current-photo').style.display = 'none';

                    fetch('{{ url('master/programs/' . ($data->id ?? '') . '/remove-photo') }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Foto berhasil dihapus', 'success');
                            } else {
                                showNotification('Gagal menghapus foto', 'error');
                            }
                        })
                        .catch(() => {
                            showNotification('Terjadi kesalahan server', 'error');
                        });
                }
            });
        }

        function showNotification(message, type) {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Berhasil' : 'Gagal',
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        }
    </script>
@endsection
