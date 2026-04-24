<?php
$isSuperAdmin = isSuperAdmin();
$userinfo = Session::get('userinfo');
$userId = getUserID();

$allowedPrograms = [];
if (!$isSuperAdmin && $userId) {
    $allowedPrograms = \App\Models\UserProgram::where('user_id', $userId)->with('program')->get()->pluck('program');
}
?>

<ul class="menu-inner py-1">

    <li class="menu-item <?= activemenu('dashboard') ?>">
        <a href="<?= url('dashboard') ?>" class="menu-link">
            <i class="menu-icon ri ri-home-4-line"></i>
            <div>Dashboard</div>
        </a>
    </li>

    <?php if ($isSuperAdmin): ?>
    <li class="menu-header">
        <span class="menu-header-text">Master Data</span>
    </li>

    <li class="menu-item <?= activemenu('master/users') ?>">
        <a href="<?= url('master/users') ?>" class="menu-link">
            <i class="menu-icon ri ri-user-settings-line"></i>
            <div>Manajemen User</div>
        </a>
    </li>

    <li class="menu-item <?= activemenu('master/programs') ?>">
        <a href="<?= url('master/programs') ?>" class="menu-link">
            <i class="menu-icon ri ri-apps-2-line"></i>
            <div>Manajemen Program</div>
        </a>
    </li>

    <li class="menu-item <?= activemenu('master/menus') ?>">
        <a href="<?= url('master/menus') ?>" class="menu-link">
            <i class="menu-icon ri ri-menu-2-line"></i>
            <div>Manajemen Menu</div>
        </a>
    </li>

    <li class="menu-header">
        <span class="menu-header-text">Permission</span>
    </li>

    <li class="menu-item <?= activemenu('master/user-menu-permissions') ?>">
        <a href="<?= url('master/user-menu-permissions') ?>" class="menu-link">
            <i class="menu-icon ri ri-key-2-line"></i>
            <div>Permission Menu</div>
        </a>
    </li>

    <li class="menu-item <?= activemenu('master/user-program-permissions') ?>">
        <a href="<?= url('master/user-program-permissions') ?>" class="menu-link">
            <i class="menu-icon ri ri-shield-user-line"></i>
            <div>Permission Program</div>
        </a>
    </li>
    <?php endif; ?>

</ul>
