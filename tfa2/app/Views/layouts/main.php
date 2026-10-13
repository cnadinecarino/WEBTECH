<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | Simple POS</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
    <?php $currentPath = trim(service('uri')->getPath(), '/'); ?>
    <header class="site-header">
        <div class="nav-container">
            <a class="brand" href="<?= site_url('/') ?>">Simple POS</a>
            <nav aria-label="Main navigation">
                <a class="<?= $currentPath === '' ? 'active' : '' ?>" href="<?= site_url('/') ?>" <?= $currentPath === '' ? 'aria-current="page"' : '' ?>>Home</a>
                <a class="<?= $currentPath === 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>" <?= $currentPath === 'about' ? 'aria-current="page"' : '' ?>>About</a>
                <a class="<?= $currentPath === 'customers' ? 'active' : '' ?>" href="<?= site_url('customers') ?>" <?= $currentPath === 'customers' ? 'aria-current="page"' : '' ?>>Customer Accounts</a>
                <a class="<?= $currentPath === 'users' ? 'active' : '' ?>" href="<?= site_url('users') ?>" <?= $currentPath === 'users' ? 'aria-current="page"' : '' ?>>User Accounts</a>
            </nav>
        </div>
    </header>

    <main class="page-container">
        <section class="content-card">
            <?= $this->renderSection('content') ?>
        </section>
    </main>

    <footer>&copy; <?= date('Y') ?> Simple POS TFA2 Laboratory Project</footer>
</body>
</html>
