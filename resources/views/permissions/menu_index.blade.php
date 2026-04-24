@extends('layouts.main')
@section('title', 'Permission Menu')

@section('content')
<div class="col-xxl">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Permission Menu per User</h5>
        </div>
        <div class="card-datatable">
            @include('partials.notification')
            @include('partials.error')
            <div class="table-responsive p-2">
                <table class="datatables-basic table table-bordered nowrap">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Level</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $user)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->level }}</td>
                            <td>
                                <a href="{{ url('/master/user-menu-permissions/' . $user->id . '/edit') }}" class="btn btn-sm btn-warning">
                                    <i class="ri ri-key-line"></i> Set Permission
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    $('.datatables-basic').DataTable({ pageLength: 25 });
});
</script>
@endsection
