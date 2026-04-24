<nav class="layout-navbar container-fluid navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base ri ri-menu-line icon-md"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
            </div>
        </div>
        <!-- /Search -->
        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
            <!-- Title -->
            <li class="nav-item me-3">
                <h4 class="mb-0 text-primary"><?= getData('title') ?></h4>
            </li>
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="<?= url('imgs/avatars/1.png') ?>" alt="alt" class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="<?= url('imgs/avatars/1.png') ?>" alt="alt"
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <?= $nama ?>
                                    <h6 class="mb-0"></h6>
                                    <small class="text-body-secondary"><?= userlevel($level) ?></small>
                                </div>
                            </div>
                        </a>
                    </li>
                    {{-- <li>
				<div class="dropdown-divider my-1"></div>
			</li>
			<li>
				<a class="dropdown-item" href="#">
				<i class="icon-base ri ri-user-line icon-md me-3"></i>
				<span>My Profile</span>
				</a>
			</li> --}}
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <div class="d-grid px-4 pt-2 pb-1">
                            <a class="btn btn-danger d-flex" href="<?= url('/logout') ?>">
                                <small class="align-middle">Logout</small>
                                <i class="ri ri-logout-box-r-line ms-2 ri-xs"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
