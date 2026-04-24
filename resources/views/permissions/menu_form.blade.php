@extends('layouts.main')
@section('title', 'Permission Menu')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Permission Menu — {{ $user->nama }} ({{ $user->username }})</h5>
            <a href="{{ url('/master/user-menu-permissions') }}" class="btn btn-primary">Kembali</a>
        </div>

        <div class="card-body">
            @include('partials.error')

            <form method="POST" action="{{ url('master/user-menu-permissions/' . $user->id) }}">
                @csrf
                @method('PUT')

                @foreach ($programs as $program)
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-1">{{ $program->name }}</h6>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">View</th>
                                        <th class="text-center">Insert</th>
                                        <th class="text-center">Update</th>
                                        <th class="text-center">Delete</th>
                                        <th class="text-center">
                                            <div class="check-all-wrapper">
                                                <input type="checkbox" class="master-check"
                                                    data-program="{{ $program->id }}" id="checkAll{{ $program->id }}">
                                                <label for="checkAll{{ $program->id }}">All</label>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($program->menus as $menu)
                                        @php $perm = $existing->get($menu->id); @endphp
                                        <tr>
                                            <td>{{ $menu->name }}</td>

                                            @foreach (['view', 'insert', 'update', 'delete'] as $action)
                                                <td class="text-center">
                                                    <label class="custom-check">
                                                        <input type="checkbox"
                                                            name="permissions[{{ $menu->id }}][can_{{ $action }}]"
                                                            class="perm-cb prog-{{ $program->id }}"
                                                            {{ $perm && $perm->{'can_' . $action} ? 'checked' : '' }}>
                                                        <span></span>
                                                    </label>
                                                </td>
                                            @endforeach

                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Permission</button>
                    <a href="{{ url('/master/user-menu-permissions') }}" class="btn btn-warning">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('style')

    <style>
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 0;
        }

        .table thead {
            background: #f8fafc;
        }

        .table th {
            font-weight: 600;
            font-size: 13px;
            color: #555;
        }

        .table td {
            vertical-align: middle;
        }

        h6.text-primary {
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .custom-check {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .custom-check input {
            display: none;
        }

        .custom-check span {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 5px;
            display: inline-block;
            transition: all 0.2s ease;
            background: #fff;
        }

        .custom-check input:checked+span {
            background: #4f46e5;
            border-color: #4f46e5;
        }

        .custom-check span::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 6px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            opacity: 0;
            transition: 0.2s;
        }

        .custom-check input:checked+span::after {
            opacity: 1;
        }

        .custom-check:hover span {
            border-color: #6366f1;
        }

        .check-all-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .master-check {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .master-check:checked {
            transform: scale(1.2);
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        .table-responsive {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }
    </style>
@endsection

@section('script')
    <script>
        document.querySelectorAll('.master-check').forEach(master => {
            master.addEventListener('change', function() {
                let progId = this.dataset.program;
                let checkboxes = document.querySelectorAll('.prog-' + progId);

                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        });

        document.querySelectorAll('.perm-cb').forEach(cb => {
            cb.addEventListener('change', function() {
                let progId = this.classList[1].split('-')[1];
                let all = document.querySelectorAll('.prog-' + progId);
                let master = document.querySelector('#checkAll' + progId);

                master.checked = Array.from(all).every(c => c.checked);
            });
        });
    </script>
@endsection
