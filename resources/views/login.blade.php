<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-wide" data-assets-path="<?= url('assets/') ?>"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title> <?= getData('title') ?> | Login</title>
    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="<?= url('imgs/avian.png') ?>">
    <link rel="shortcut icon" href="<?= url('imgs/avian.png') ?>">
    <!-- Fonts -->
    <link rel="stylesheet" href="<?= url('assets/vendor/fonts/iconify-icons.css') ?>" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
    <!-- endbuild -->
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?= url('assets/vendor/css/pages/page-auth.css') ?>" />
    <style>
        .navbar_batik_footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            line-height: 0;
            z-index: 10;
        }

        .navbar_batik_footer .batik_img {
            width: 100%;
            display: block;
        }

        .authentication-wrapper {
            padding-bottom: 80px !important;
        }
    </style>
    <!-- Helpers -->
    <script src="<?= url('assets/vendor/js/helpers.js') ?>"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->
    <script src="<?= url('assets/js/config.js') ?>"></script>
</head>

<body>
    <div class="navbar_batik"><img class="batik_img" src="<?= url('imgs/w-2500.svg') ?>"></div>
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6 mx-4">
            <!-- Login -->
            <div class="card p-sm-7 p-2">
                <!-- Logo -->
                <div class="app-brand justify-content-center mt-5">
                    <a href="#" class="app-brand-link gap-3">
                        <span class="text-primary"><img src="<?= url('imgs/avian.png') ?>" width="60px;"></span>
                        <img src="<?= url('imgs/logo.png') ?>"; style="max-width:140px;">
                    </a>
                </div>
                <!-- /Logo -->
                <div class="card-body mt-1">
                    <h4 class="mb-1">{{ getData('title') }}</h4>
                    <p class="mb-5">Silahkan masuk ke akun anda</p>
                    @if (session('message_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <form id="formLogin" class="mb-5" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="error-alert"></div>
                        <div class="form-floating form-floating-outline mb-5 form-control-validation">
                            <input type="text" class="form-control" id="email" name="username"
                                placeholder="Masukkan username" autofocus />
                            <label for="email">Username</label>
                        </div>
                        <div class="mb-5">
                            <div class="form-password-toggle form-control-validation">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password" class="form-control" name="password"
                                            placeholder="Masukkan password" aria-describedby="password" />
                                        <label for="password">Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base ri ri-eye-off-line icon-20px"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <button class="btn btn-primary d-grid w-100" type="submit">login</button>
                        </div>
                    </form>
                    <p class="text-center mb-5">
                        <span>Jika mengalami kendala harap hubungi email berikut :</span>
                        <a href="mailto:it_web@avianbrands.comm">
                            <span>it_web@avianbrands.comm</span>
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>
    <!-- / Content -->
    <div class="navbar_batik_footer"><img class="batik_img" src="<?= url('imgs/w-2500.svg') ?>"></div>
    <!-- Core JS -->
    <script src="<?= url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/node-waves/node-waves.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
    <script src="<?= url('assets/vendor/js/menu.js') ?>"></script>
    <!-- endbuild -->
    <!-- Vendors JS -->
    <!-- Main JS -->
    <script src="<?= url('assets/js/main.js') ?>"></script>
    <!-- Page JS -->
    <script type="text/javascript">
        document.documentElement.style.setProperty('--' + window.Helpers.prefix + 'primary', '#0D9394');
        const routes = {
            login: "{{ route('login') }}",
            dashboard: "{{ route('dashboard') }}",
            changePassword: "{{ route('change.password.form') }}"
        };

        $("#formLogin").submit(function() {
            let frm_data = $(this).serialize();

            $.ajax({
                type: "POST",
                url: routes.login,
                data: frm_data,

                success: function(response) {
                    if (response.status) {
                        window.location.href = routes.dashboard;

                    } else if (response.expired) {
                        let locked = response.locked ? '&locked=1' : '';

                        window.location.href = routes.changePassword +
                            "?username=" + encodeURIComponent(response.username) + locked;

                    } else {
                        $('.error-alert').html("");

                        $.each(response.message, function(key, value) {
                            $('.error-alert').append(`
                            <div class="alert alert-danger alert-dismissible fade show">
                                ${value}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                        });
                    }
                },

                error: function() {
                    alert('Ada kesalahan login. Silahkan coba lagi');
                    location.reload();
                }
            });

            return false;
        });
    </script>
    @yield('script')
</body>

</html>
