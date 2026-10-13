<?php
$currentController = service('router')->controllerName();
$currentMethod = service('router')->methodName();
$navigation = [
    ['path' => '', 'label' => 'Home', 'active' => str_ends_with($currentController, '\\Pages') && $currentMethod === 'index'],
    ['path' => 'about', 'label' => 'About', 'active' => str_ends_with($currentController, '\\Pages') && $currentMethod === 'about'],
    ['path' => 'customers', 'label' => 'Customer Accounts', 'active' => str_ends_with($currentController, '\\Customers')],
    ['path' => 'users', 'label' => 'User Accounts', 'active' => str_ends_with($currentController, '\\Users')],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | Simple POS</title>
    <link rel="stylesheet" href="<?= esc(base_url('assets/styles.css'), 'attr') ?>">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="nav-wrap">
            <a class="brand" href="<?= esc(site_url(), 'attr') ?>">Simple POS</a>
            <nav aria-label="Main navigation">
                <?php foreach ($navigation as $item): ?>
                    <a href="<?= esc(site_url($item['path']), 'attr') ?>"<?= $item['active'] ? ' aria-current="page"' : '' ?>><?= esc($item['label']) ?></a>
                <?php endforeach ?>
            </nav>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
        <?php if ($message = session()->getFlashdata('success')): ?>
            <div class="notice notice-success" role="status"><?= esc($message) ?></div>
        <?php endif ?>
        <?php if ($message = session()->getFlashdata('error')): ?>
            <div class="notice notice-error" role="alert"><?= esc($message) ?></div>
        <?php endif ?>
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="site-footer">
        <small>&copy; <?= date('Y') ?> Simple POS &middot; Customer and account management</small>
    </footer>
</body>
</html>
