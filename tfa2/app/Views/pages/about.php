<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h1>About the POS System</h1>
<p class="lead">
    Simple POS is a classroom project that demonstrates how CodeIgniter 4 routes,
    controllers, Models, and views work together using the MVC architecture.
</p>
<p>
    Customer and user records are retrieved from MySQL through CodeIgniter Models.
    Controllers prepare the records and views present them safely.
</p>
<?= $this->endSection() ?>
