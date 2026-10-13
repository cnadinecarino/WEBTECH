<?= $this->include('templates/header') ?>

<section class="card">
    <h1>Welcome to Simple POS</h1>
    <p class="lead">
        A basic Point-of-Sale website for viewing customer and staff account information.
        Use the navigation links to explore the four required pages.
    </p>
    <div class="actions">
        <a class="button" href="<?= site_url('customers') ?>">View Customers</a>
        <a class="button" href="<?= site_url('users') ?>">View Users</a>
    </div>
</section>

<?= $this->include('templates/footer') ?>
