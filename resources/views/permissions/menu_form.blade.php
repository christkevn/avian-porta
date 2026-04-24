@extends('layouts.main')
@section('title', 'Permission Menu')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Permission Menu — {{ $user->nama }} ({{ $user->username }})</h5>
            <small class="text-body-secondary float-end">
                <a href="{{ url('/master/user-menu-permissions') }}" class="btn btn-primary w-200">Kembali</a>
            </small>
        </div>

        <div class="card-body">
            @include('partials.error')

            <form method="POST" action="{{ url('master/user-menu-permissions/' . $user->id) }}" id="permissionForm">
                @csrf
                @method('PUT')

                <ul class="nav nav-tabs mb-3" id="programTab" role="tablist">
                    @foreach ($programs as $index => $program)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index == 0 ? 'active' : '' }}" data-bs-toggle="tab"
                                data-bs-target="#program{{ $program->id }}" type="button" role="tab">
                                {{ $program->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach ($programs as $index => $program)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="program{{ $program->id }}"
                            role="tabpanel">

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%">Menu</th>
                                            <th class="text-center" style="width: 15%">
                                                View
                                                <input type="checkbox" class="master-col-check"
                                                    data-program="{{ $program->id }}" data-action="view">
                                            </th>
                                            <th class="text-center" style="width: 15%">
                                                Insert
                                                <input type="checkbox" class="master-col-check"
                                                    data-program="{{ $program->id }}" data-action="insert">
                                            </th>
                                            <th class="text-center" style="width: 15%">
                                                Update
                                                <input type="checkbox" class="master-col-check"
                                                    data-program="{{ $program->id }}" data-action="update">
                                            </th>
                                            <th class="text-center" style="width: 15%">
                                                Delete
                                                <input type="checkbox" class="master-col-check"
                                                    data-program="{{ $program->id }}" data-action="delete">
                                            </th>
                                            <th class="text-center" style="width: 10%">
                                                All Menu
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($program->menus as $menu)
                                            @php $perm = $existing->get($menu->id); @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $menu->name }}</strong>
                                                    @if ($menu->description)
                                                        <br><small class="text-muted">{{ $menu->description }}</small>
                                                    @endif
                                                </td>

                                                @foreach (['view', 'insert', 'update', 'delete'] as $action)
                                                    <td class="text-center">
                                                        <input type="checkbox"
                                                            name="permissions[{{ $menu->id }}][can_{{ $action }}]"
                                                            class="perm-cb prog-{{ $program->id }} col-{{ $action }}"
                                                            data-program="{{ $program->id }}"
                                                            data-action="{{ $action }}"
                                                            {{ $perm && $perm->{'can_' . $action} ? 'checked' : '' }}>
                                                    </td>
                                                @endforeach

                                                <td class="text-center">
                                                    <input type="checkbox" class="row-master-check"
                                                        data-program="{{ $program->id }}"
                                                        data-menu="{{ $menu->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <div class="d-flex justify-content-end flex-wrap flex-sm-nowrap gap-2">
                            <button type="submit" class="btn btn-primary w-100 w-200">Simpan</button>
                            <a href="{{ url('/master/user-menu-permissions') }}" type="button"
                                class="btn btn-warning w-100 w-200">Batal</a>
                        </div>
                    </div>
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

        .nav-tabs .nav-link {
            font-weight: 500;
            color: #4b5563;
            border: none;
            border-bottom: 2px solid transparent;
        }

        .nav-tabs .nav-link.active {
            color: #4f46e5;
            border-bottom: 2px solid #4f46e5;
            background: transparent;
        }

        .nav-tabs .nav-link:hover {
            border-bottom: 2px solid #c7d2fe;
            color: #6366f1;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background: #f8fafc;
        }

        .table th {
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        .table-responsive {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #4f46e5;
        }

        .master-col-check {
            margin-left: 8px;
            transform: scale(0.9);
        }

        .row-master-check {
            accent-color: #059669;
        }

        .text-muted {
            font-size: 11px;
        }

        .btn-primary {
            background: #4f46e5;
            border: none;
            padding: 8px 24px;
        }

        .btn-primary:hover {
            background: #4338ca;
        }
    </style>
@endsection

@section('script')
    <script>
        function updateRowMaster(programId, menuId) {
            let row = document.querySelector(`input[data-program="${programId}"][data-menu="${menuId}"]`).closest('tr');
            let checkboxes = row.querySelectorAll('.perm-cb');
            let rowMaster = row.querySelector('.row-master-check');

            if (rowMaster) {
                let allChecked = Array.from(checkboxes).every(cb => cb.checked);
                rowMaster.checked = allChecked;
            }
        }

        function updateColumnMaster(programId, action) {
            let checkboxes = document.querySelectorAll(`.prog-${programId}.col-${action}`);
            let allChecked = Array.from(checkboxes).every(cb => cb.checked);
            let masterCheck = document.querySelector(
                `.master-col-check[data-program="${programId}"][data-action="${action}"]`);

            if (masterCheck) {
                masterCheck.checked = allChecked;
            }
        }

        function checkAllRowsInProgram(programId) {
            let rows = document.querySelectorAll(`.prog-${programId}`).forEach(cb => {
                let row = cb.closest('tr');
                let menuId = row.querySelector('.row-master-check')?.dataset.menu;
                if (menuId) {
                    updateRowMaster(programId, menuId);
                }
            });
        }

        document.querySelectorAll('.row-master-check').forEach(master => {
            master.addEventListener('change', function() {
                let programId = this.dataset.program;
                let row = this.closest('tr');
                let checkboxes = row.querySelectorAll('.perm-cb');
                let isChecked = this.checked;

                checkboxes.forEach(cb => {
                    cb.checked = isChecked;
                    let action = cb.dataset.action;
                    updateColumnMaster(programId, action);
                });
            });
        });

        document.querySelectorAll('.master-col-check').forEach(master => {
            master.addEventListener('change', function() {
                let programId = this.dataset.program;
                let action = this.dataset.action;
                let isChecked = this.checked;

                let checkboxes = document.querySelectorAll(`.prog-${programId}.col-${action}`);
                checkboxes.forEach(cb => {
                    cb.checked = isChecked;

                    let row = cb.closest('tr');
                    let menuId = row.querySelector('.row-master-check')?.dataset.menu;
                    if (menuId) {
                        updateRowMaster(programId, menuId);
                    }
                });
            });
        });

        document.querySelectorAll('.perm-cb').forEach(cb => {
            cb.addEventListener('change', function() {
                let programId = this.dataset.program;
                let action = this.dataset.action;
                let row = this.closest('tr');
                let menuId = row.querySelector('.row-master-check')?.dataset.menu;

                if (menuId) {
                    updateRowMaster(programId, menuId);
                }

                updateColumnMaster(programId, action);
            });
        });

        document.querySelectorAll('.perm-cb').forEach(() => {
            let programs = [...new Set(Array.from(document.querySelectorAll('.perm-cb')).map(cb => cb.dataset
                .program))];
            programs.forEach(programId => {
                ['view', 'insert', 'update', 'delete'].forEach(action => {
                    updateColumnMaster(programId, action);
                });
                checkAllRowsInProgram(programId);
            });
        });
    </script>
@endsection
