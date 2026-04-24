<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-wide" data-assets-path="<?= url('assets/') ?>"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .password-requirements {
            background-color: #f9f9f9;
            border-left: 3px solid #007bff;
            padding: 12px 15px;
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 13px;
            border-radius: 6px;
        }

        .requirement-item {
            padding: 4px 0;
            transition: color 0.2s;
            font-size: 12px;
        }

        .requirement-item.valid {
            color: #28a745;
        }

        .requirement-item.invalid {
            color: #dc3545;
        }

        .requirement-item i {
            width: 18px;
            margin-right: 5px;
        }

        .input-group-append {
            cursor: pointer;
        }

        .password-toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
            color: #6c757d;
        }

        .form-floating {
            position: relative;
        }

        .form-floating .form-control {
            padding-right: 40px;
        }

        .alert-hint {
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
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
                        <img src="<?= url('imgs/logo.png') ?>" style="max-width:140px;">
                    </a>
                </div>
                <div class="card-body mt-1">
                    <h4 class="mb-1">Ganti Password</h4>
                    <p class="mb-4 text-danger">
                        <i class="ri ri-error-warning-line"></i>
                        @if (request()->query('locked'))
                            Akun Anda terkunci karena 3x salah password. Silahkan ganti password untuk membuka akun.
                        @else
                            Password Anda sudah kadaluarsa. Silahkan ganti password untuk melanjutkan.
                        @endif
                    </p>

                    @if (session('message_success'))
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

                        <div class="form-floating form-floating-outline mb-2 position-relative">
                            <input type="password" class="form-control" name="password_new" id="passwordNew"
                                placeholder="Password Baru" required minlength="6">
                            <label>Password Baru</label>
                            <i class="fas fa-eye-slash password-toggle-icon" id="togglePasswordNew"></i>
                        </div>

                        <div class="password-requirements" id="passwordRequirements" style="display: none;">
                            <strong>Password harus memenuhi:</strong>
                            <div class="requirement-item" id="req-length">
                                <i class="fas fa-circle"></i> Minimal 6 karakter (disarankan 12+)
                            </div>
                            <div class="requirement-item" id="req-upper">
                                <i class="fas fa-circle"></i> Minimal 1 huruf besar (A-Z)
                            </div>
                            <div class="requirement-item" id="req-lower">
                                <i class="fas fa-circle"></i> Minimal 1 huruf kecil (a-z)
                            </div>
                            <div class="requirement-item" id="req-number">
                                <i class="fas fa-circle"></i> Minimal 1 angka (0-9)
                            </div>
                            <div class="requirement-item" id="req-symbol">
                                <i class="fas fa-circle"></i> Minimal 1 simbol (!@#$%^&* dll)
                            </div>
                        </div>

                        <div class="form-floating form-floating-outline mb-5 position-relative">
                            <input type="password" class="form-control" name="password_new_confirmation"
                                id="passwordConfirm" placeholder="Konfirmasi Password" required>
                            <label>Konfirmasi Password Baru</label>
                            <i class="fas fa-eye-slash password-toggle-icon" id="togglePasswordConfirm"></i>
                        </div>

                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger">{{ $error }}</div>
                            @endforeach
                        @endif

                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit" id="submitBtn">Simpan Password
                                Baru</button>
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

    <script>
        const passwordNew = document.getElementById('passwordNew');
        const passwordConfirm = document.getElementById('passwordConfirm');
        const toggleNew = document.getElementById('togglePasswordNew');
        const toggleConfirm = document.getElementById('togglePasswordConfirm');
        const requirementsDiv = document.getElementById('passwordRequirements');
        const submitBtn = document.getElementById('submitBtn');

        function updateRequirement(id, isValid) {
            const elem = document.getElementById(id);
            if (!elem) return;
            const icon = elem.querySelector('i');
            if (isValid) {
                elem.classList.remove('invalid');
                elem.classList.add('valid');
                icon.className = 'fas fa-check-circle';
            } else {
                elem.classList.remove('valid');
                elem.classList.add('invalid');
                icon.className = 'fas fa-times-circle';
            }
        }

        function validatePassword() {
            const password = passwordNew.value;

            if (password.length === 0) {
                requirementsDiv.style.display = 'none';
                const reqs = ['req-length', 'req-upper', 'req-lower', 'req-number', 'req-symbol'];
                reqs.forEach(id => {
                    const elem = document.getElementById(id);
                    if (elem) {
                        elem.classList.remove('valid', 'invalid');
                        const icon = elem.querySelector('i');
                        icon.className = 'fas fa-circle';
                    }
                });
                validateConfirmPassword();
                return;
            }

            requirementsDiv.style.display = 'block';

            const hasLength = password.length >= 6;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

            updateRequirement('req-length', hasLength);
            updateRequirement('req-upper', hasUpper);
            updateRequirement('req-lower', hasLower);
            updateRequirement('req-number', hasNumber);
            updateRequirement('req-symbol', hasSymbol);

            if (hasLength && hasUpper && hasLower && hasNumber && hasSymbol) {
                passwordNew.style.borderColor = '#28a745';
            } else {
                passwordNew.style.borderColor = '#dc3545';
            }

            validateConfirmPassword();
        }

        function validateConfirmPassword() {
            const password = passwordNew.value;
            const confirm = passwordConfirm.value;

            if (confirm.length === 0) {
                passwordConfirm.style.borderColor = '';
                return;
            }

            if (password === confirm && password.length > 0) {
                passwordConfirm.style.borderColor = '#28a745';
            } else {
                passwordConfirm.style.borderColor = '#dc3545';
            }
        }

        function checkAllRequirements() {
            const password = passwordNew.value;
            const confirm = passwordConfirm.value;

            const hasLength = password.length >= 6;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
            const isMatch = (password === confirm) && password.length > 0;

            const allValid = hasLength && hasUpper && hasLower && hasNumber && hasSymbol && isMatch;

            if (!allValid && password.length > 0) {
                let errorMsg = '';
                if (!hasLength) errorMsg = 'Password minimal 6 karakter. ';
                else if (!hasUpper) errorMsg = 'Password harus mengandung huruf besar. ';
                else if (!hasLower) errorMsg = 'Password harus mengandung huruf kecil. ';
                else if (!hasNumber) errorMsg = 'Password harus mengandung angka. ';
                else if (!hasSymbol) errorMsg = 'Password harus mengandung simbol. ';
                else if (!isMatch) errorMsg = 'Konfirmasi password tidak cocok. ';

                if (errorMsg) {
                    const errorDiv = document.querySelector('.error-alert');
                    if (errorDiv) {
                        errorDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
                    }
                }
                return false;
            }

            const errorDiv = document.querySelector('.error-alert');
            if (errorDiv) errorDiv.innerHTML = '';
            return true;
        }

        passwordNew.addEventListener('input', function() {
            validatePassword();
        });

        passwordConfirm.addEventListener('input', function() {
            validateConfirmPassword();
        });

        toggleNew.addEventListener('click', function() {
            const type = passwordNew.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordNew.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        toggleConfirm.addEventListener('click', function() {
            const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirm.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        const form = document.getElementById('formChangePass');
        form.addEventListener('submit', function(e) {
            if (!checkAllRequirements()) {
                e.preventDefault();
                const errorDiv = document.querySelector('.error-alert');
                if (errorDiv && errorDiv.innerHTML !== '') {
                    errorDiv.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });

        if (passwordNew.value.length > 0) {
            validatePassword();
        }
    </script>
</body>

</html>
