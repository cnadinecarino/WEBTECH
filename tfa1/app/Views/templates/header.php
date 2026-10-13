<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | Simple POS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <header class="site-header">
        <nav class="navbar" aria-label="Main navigation">
            <a class="brand" href="<?= site_url('/') ?>">Simple POS</a>
            <div class="nav-links">
                <a href="<?= site_url('/') ?>">Home</a>
                <a href="<?= site_url('about') ?>">About</a>
                <a href="<?= site_url('customers') ?>">Customer Accounts</a>
                <a href="<?= site_url('users') ?>">User Accounts</a>
            </div>
        </nav>
    </header>

    <main class="container">
