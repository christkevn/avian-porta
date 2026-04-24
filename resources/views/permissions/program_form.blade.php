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

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <div class="d-flex justify-content-end flex-wrap flex-sm-nowrap gap-2">
                            <button type="submit" class="btn btn-primary w-100 w-200">Simpan</button>
                            <a href="{{ url('/master/user-program-permissions') }}" type="button"
                                class="btn btn-warning w-100 w-200">Batal</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
