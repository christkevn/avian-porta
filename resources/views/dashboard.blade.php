@extends('layouts.main')

@section('title', 'Dashboard')

@section('css')
    <style>
        .program-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .program-card {
            position: relative;
            width: 100%;
            height: 200px;
            border-radius: 16px;
            overflow: hidden;
            text-decoration: none;
            display: block;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .program-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .program-bg {
            position: absolute;
            inset: 0;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #fff;
            transition: transform 0.4s ease;
        }

        .program-card:hover .program-bg {
            transform: scale(1.05);
        }

        .program-name {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.75), transparent);
            text-align: center;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Selamat Datang, {{ Session::get('userinfo')['nama'] ?? 'User' }}!</h5>
                </div>
            </div>
        </div>
    </div>

    @php
        $isSuperAdmin = isSuperAdmin();
        $userId = getUserID();
        $programs = collect();

        if ($isSuperAdmin) {
            $programs = \App\Models\Program::orderBy('name')->get();
        } elseif ($userId) {
            $programs = \App\Models\UserProgram::where('user_id', $userId)
                ->with('program')
                ->get()
                ->pluck('program')
                ->filter()
                ->sortBy('name');
        }
    @endphp

    @if ($programs->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="section-title">
                    <i class="ri ri-apps-2-line me-2"></i>
                    Program yang Tersedia
                </h5>

                <div class="program-grid">
                    @foreach ($programs as $program)
                        @if ($program->url)
                            <a href="{{ Str::startsWith($program->url, 'http') ? $program->url : url($program->url) }}"
                                class="program-card"
                                target="{{ Str::startsWith($program->url, 'http') ? '_blank' : '_self' }}">

                                @php
                                    $photo = $program->photo_url;

                                    if ($photo) {
                                        $photoUrl = Str::startsWith($photo, 'http')
                                            ? $photo
                                            : asset('storage/' . $photo);
                                    } else {
                                        $photoUrl = asset('default.jpg');
                                    }
                                @endphp

                                <div class="program-bg" style="background-image: url('{{ $photoUrl }}')">
                                </div>

                                <div class="program-name">
                                    {{ $program->name }}
                                </div>

                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @elseif(!$isSuperAdmin)
        <div class="row mt-4">
            <div class="col-12">
                <div class="empty-programs">
                    <i class="ri ri-folder-forbid-line" style="font-size: 64px; color: #ccc;"></i>
                    <h5 class="mt-3">Belum Ada Program</h5>
                    <p class="text-muted">Anda belum memiliki akses ke program manapun. Silahkan hubungi administrator.</p>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('script')
    <script></script>
@endsection
