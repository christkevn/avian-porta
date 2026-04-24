<?php
$userinfo = Session::get('userinfo');
$username = $userinfo['username'];
$level = $userinfo['level'];
$nama = $userinfo['nama'];
?>

<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-wide" data-assets-path="<?= url('assets/') ?>"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title> <?= getData('title') ?> | @yield('title')</title>
    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="<?= url('imgs/avian.png') ?>">
    <link rel="shortcut icon" href="<?= url('imgs/avian.png') ?>">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="<?= url('assets/vendor/fonts/iconify-icons.css') ?>" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/datatables/datatables.bootstrap5.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/datatables/responsive.bootstrap5.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/datatables/buttons.bootstrap5.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/datatables/rowgroup.bootstrap5.css') ?>" />
    <link rel="stylesheet" href="<?= url('assets/vendor/libs/flatpickr/flatpickr.min.css') ?>" />

    <!-- endbuild -->
    <!-- Page CSS -->
    @yield('css')
    <!-- Helpers -->
    <style>
        button,
        button * {
            font-size: 14px !important;
        }

        a:not(.menu-link),
        a:not(.menu-link) * {
            font-size: 0.875rem;
        }

        .swal2-confirm {
            background-color: #0D9394 !important;
            color: #fff !important;
        }

        .menu-vertical {
            font-size: 0.875rem;
        }

        .menu-vertical .menu-link {
            min-block-size: 32px;
            line-height: 1.2;
        }

        .menu-vertical .menu-icon {
            font-size: 1.275em;
            inline-size: 0.875em;
            block-size: 0.875em;
        }

        .menu-vertical .menu-header-text {
            letter-spacing: .3px;
        }

        .menu-vertical .menu-item {
            transform: scale(0.95);
            transform-origin: left center;
        }

        .menu-vertical .menu-sub .menu-item {
            transform: scale(0.94);
        }

        .menu-vertical .menu-toggle::after {
            transform: translateY(-50%) scale(0.8);
        }

        .menu-item {
            margin: 0px
        }

        .menu-vertical .menu-item .menu-link {
            font-size: 12px;
        }

        .menu-vertical .menu-inner>.menu-item {
            margin-inline: 0;
        }

        .status-col {
            width: 80px;
            max-width: 80px;
            white-space: nowrap;
            overflow: visible;
            text-overflow: unset;
            text-align: center;
        }

        .action-col {
            width: 90px;
            max-width: 90px;
            white-space: nowrap;
            overflow: visible;
            text-overflow: unset;
            text-align: center;
        }

        .action-col .btn {
            padding: 6px 10px;
            height: 34px;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        th,
        th * {
            padding: 10px 0px !important;
            margin: 0 !important;
        }

        .table.dataTable {
            font-size: 14px !important;
        }

        table td:first-child,
        table th:first-child {
            padding-left: 16px !important;
        }
    </style>
    <script src="<?= url('assets/vendor/js/helpers.js') ?>"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->
    <script src="<?= url('assets/js/config.js') ?>"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="navbar_batik"><img class="batik_img" src="<?= url('imgs/w-2500.svg') ?>"></div>
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand">
                    <a href="{{ url('/') }}" class="app-brand-link">
                        <span class="app-brand-logo me-1"><span class="text-primary"><img
                                    src="<?= url('imgs/avian.png') ?>" width="40px;"></span></span>
                        <span class="app-brand-text menu-text fw-semibold ms-2"><img src="<?= url('imgs/logo.png') ?>";
                                style="max-width:90px;"></span>
                    </a>
                </div>
                <div class="menu-inner-shadow"></div>
                @include('partials.menu')
            </aside>
            <!-- / Menu -->
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('partials.navbar')
                <!-- / Navbar -->
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-fluid flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->
                    <!-- Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>

            <!-- / Layout page -->
            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
    </div>
    @include('partials.footer')
    <!-- / Footer -->
    <div class="navbar_batik_footer"><img class="batik_img" src="<?= url('imgs/w-2500.svg') ?>"></div>
    <!-- / Layout wrapper -->
    <!-- Core JS -->
    <script src="<?= url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= url('assets/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/node-waves/node-waves.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/datatables/datatables-bootstrap5.js') ?>"></script>
    <script src="<?= url('assets/vendor/js/menu.js') ?>"></script>
    <script src="<?= url('assets/vendor/libs/flatpickr/flatpickr.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="<?= url('assets/vendor/libs/select2/select2.min.css') ?>" rel="stylesheet" />
    <script src="<?= url('assets/vendor/libs/select2/select2.min.js') ?>"></script>
    <!-- endbuild -->
    <!-- Vendors JS -->
    <!-- Main JS -->
    <script src="<?= url('assets/js/main.js') ?>"></script>
    <!-- Page JS -->
    @include('partials.script')
    @yield('script')
</body>

</html>
