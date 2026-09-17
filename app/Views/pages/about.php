<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="panel">
    <h1>About the POS System</h1>
    <p class="lead">
        Simple POS is a classroom project that demonstrates how CodeIgniter 4 routes,
        controllers, and views work together using the MVC architecture.
    </p>
    <p>
        Customer and user records are stored in temporary static PHP arrays in their
        controllers. This project does not connect to or use a database.
    </p>
</section>
<?= $this->endSection() ?>
