<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?> · Tasks for Today</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <a class="brand" href="<?= site_url('/') ?>"><span class="brand-mark">T</span><span>Tasks for Today<small>Personal workspace</small></span></a>
        <nav aria-label="Main navigation">
            <?php foreach (['home' => ['Home', '/'], 'tasks' => ['Tasks', '/tasks'], 'profile' => ['Profile', '/profile'], 'about' => ['About', '/about']] as $key => [$label, $path]): ?>
                <a href="<?= site_url($path) ?>" class="<?= $activePage === $key ? 'active' : '' ?>" <?= $activePage === $key ? 'aria-current="page"' : '' ?>><?= esc($label) ?></a>
            <?php endforeach ?>
        </nav>
    </header>
    <main class="main-content">
