@extends('layouts.main')
@section('title', 'Permission Program')

@section('content')
    <div class="col-xxl">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Permission Program per User</h5>
            </div>
            <div class="card-datatable">
                @include('partials.notification')
                @include('partials.error')
                <div class="table-responsive p-2">
                    <table class="datatables-basic table table-bordered w-100">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 1%;">No</th>
                                <th class="d-none d-md-table-cell">Username</th>
                                <th>Nama</th>
                                <th class="d-none d-md-table-cell">Level</th>
                                <th style="min-width: 220px;">Program yang Dapat Diakses</th>
                                <th class="text-nowrap" style="width: 1%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $i => $user)
                                @php
                                    $progs = $userPrograms[$user->id] ?? [];
                                    $visible = array_slice($progs, 0, 3);
                                    $hidden = array_slice($progs, 3);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>

                                    <td class="d-none d-md-table-cell">{{ $user->username }}</td>

                                    <td class="text-break">
                                        {{ $user->nama }}
                                        <div class="d-md-none small text-muted">
                                            {{ $user->username }} &middot; {{ $user->level }}
                                        </div>
                                    </td>

                                    <td class="d-none d-md-table-cell">{{ $user->level }}</td>

                                    <td>
                                        @forelse ($visible as $name)
                                            <span class="badge bg-label-primary mb-1">{{ $name }}</span>
                                        @empty
                                            <span class="text-muted fst-italic">Belum ada program</span>
                                        @endforelse

                                        @if (count($hidden) > 0)
                                            <span class="badge bg-label-secondary mb-1" style="cursor: help;"
                                                title="{{ implode(', ', $hidden) }}">
                                                +{{ count($hidden) }} lainnya
                                            </span>
                                        @endif

                                        @if (count($hidden) > 0)
                                            <span class="d-none">{{ implode(' ', $hidden) }}</span>
                                        @endif
                                    </td>

                                    <td class="text-nowrap">
                                        <a href="{{ url('/master/user-program-permissions/' . $user->id . '/edit') }}"
                                            class="btn btn-sm btn-warning"
                                            aria-label="Set program untuk {{ $user->nama }}">
                                            <i class="ri ri-apps-line"></i>
                                            <span class="d-none d-sm-inline">Set Program</span>
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
        $(document).ready(function() {
            $('.datatables-basic').DataTable({
                pageLength: 25,
                columnDefs: [{
                    orderable: false,
                    targets: [4, 5]
                }]
            });
        });
    </script>
@endsection
