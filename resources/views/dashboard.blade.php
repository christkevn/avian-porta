@extends('layouts.main')

@section('title', 'Dashboard')

@section('css')
    <style>
        .program-bubbles {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }

        .program-bubble {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #0D9394 0%, #0a7a7b 100%);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(13, 147, 148, 0.3);
            cursor: pointer;
            text-align: center;
            padding: 20px;
        }

        .program-bubble:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(13, 147, 148, 0.4);
            color: white;
        }

        .program-bubble i {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .program-bubble span {
            font-size: 14px;
            font-weight: 500;
            word-break: break-word;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0D9394;
            display: inline-block;
        }

        .empty-programs {
            text-align: center;
            padding: 50px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .program-bubble {
                width: 120px;
                height: 120px;
            }

            .program-bubble i {
                font-size: 36px;
            }

            .program-bubble span {
                font-size: 12px;
            }
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
                <div class="program-bubbles">
                    @foreach ($programs as $program)
                        @if ($program->url)
                            <a href="{{ Str::startsWith($program->url, 'http') ? $program->url : url($program->url) }}"
                                class="program-bubble"
                                target="{{ Str::startsWith($program->url, 'http') ? '_blank' : '_self' }}">
                                <i class="ri ri-apps-line"></i>
                                <span>{{ $program->name }}</span>
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
    <script>
        // Tambahan script jika diperlukan
    </script>
@endsection
