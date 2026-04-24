@extends('layouts.main')
@section('title', 'Permission Program')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Permission Program — {{ $user->nama }} ({{ $user->username }})</h5>
            <small class="text-body-secondary float-end">
                <a href="{{ url('/master/user-program-permissions') }}" class="btn btn-primary w-200">Kembali</a>
            </small>
        </div>
        <div class="card-body">
            @include('partials.error')

            <form method="POST" action="{{ url('master/user-program-permissions/' . $user->id) }}">
                @csrf
                @method('PUT')

                <p class="text-muted mb-3">Centang program yang dapat diakses oleh user ini:</p>

                <div class="row">
                    @foreach ($programs as $program)
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="program_ids[]"
                                    value="{{ $program->id }}" id="prog_{{ $program->id }}"
                                    {{ in_array($program->id, $assigned) ? 'checked' : '' }}>
                                <label class="form-check-label" for="prog_{{ $program->id }}">
                                    {{ $program->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="submit" class="btn btn-primary w-200">Simpan</button>
                    <a href="{{ url('/master/user-program-permissions') }}" class="btn btn-warning w-200">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
