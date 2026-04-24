<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-wide" data-assets-path="<?= url('assets/') ?>"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title> <?= getData('title') ?> | Ganti Password</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="<?= url('imgs/avian.png') ?>">
    <link rel="shortcut icon" href="<?= url('imgs/avian.png') ?>">
    <link rel="stylesheet" href="<?= url('assets/vendor/fonts/iconify-icons.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/css/pages/page-auth.css') ?>" />
    <script src="<?= url('assets/vendor/js/helpers.js') ?>"></script>
    <script src="<?= url('assets/js/config.js') ?>"></script>
</head>

<body>
    <div class="navbar_batik"><img class="batik_img" src="<?= url('imgs/w-2500.svg') ?>"></div>
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6 mx-4">
            <div class="card p-sm-7 p-2">
                <div class="app-brand justify-content-center mt-5">
                    <a href="#" class="app-brand-link gap-3">
                        <span class="text-primary"><img src="<?= url('imgs/avian.png') ?>" width="60px;"></span>
                        <img src="<?= url('imgs/logo.png') ?>"; style="max-width:140px;">
                    </a>
                </div>
                <div class="card-body mt-1">
                    <h4 class="mb-1">Ganti Password</h4>
                    <p class="mb-4 text-danger">
                        <i class="ri ri-error-warning-line"></i>
                        @if(request()->query('locked'))
                            Akun Anda terkunci karena 3x salah password. Silahkan ganti password untuk membuka akun.
                        @else
                            Password Anda sudah kadaluarsa. Silahkan ganti password untuk melanjutkan.
                        @endif
                    </p>

                    @if(session('message_success'))
                        <div class="alert alert-success">{{ session('message_success') }}</div>
                    @endif

                    <div class="error-alert"></div>

                    <form id="formChangePass" method="POST" action="{{ url('/change-password') }}">
                        @csrf
                        <input type="hidden" name="username" value="{{ $username }}">

                        <div class="form-floating form-floating-outline mb-4">
                            <input type="text" class="form-control" value="{{ $username }}" readonly>
                            <label>Username</label>
                        </div>

                        <div class="form-floating form-floating-outline mb-4">
                            <input type="password" class="form-control" name="password_new" id="passwordNew"
                                placeholder="Password Baru" required minlength="6">
                            <label>Password Baru</label>
                        </div>

                        <div class="form-floating form-floating-outline mb-5">
                            <input type="password" class="form-control" name="password_new_confirmation" id="passwordConfirm"
                                placeholder="Konfirmasi Password" required>
                            <label>Konfirmasi Password Baru</label>
                        </div>

                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger">{{ $error }}</div>
                            @endforeach
                        @endif

                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Simpan Password Baru</button>
                        </div>
                        <div class="text-center">
                            <a href="{{ url('/login') }}" class="text-muted small">← Kembali ke Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar_batik_footer"><img class="batik_img" src="<?= url('imgs/w-2500.svg') ?>"></div>
    <script src="<?= url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
