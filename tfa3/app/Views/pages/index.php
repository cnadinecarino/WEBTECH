<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div>
        <h1>Account Management</h1>
        <p class="page-description">Keep customer details and staff profiles up to date in your POS workspace.</p>
    </div>
</div>
<div class="workspace-grid">
    <section class="panel" aria-labelledby="customers-heading">
        <h2 id="customers-heading">Customer Accounts</h2>
        <p class="muted">Find a customer, add their contact information, or update an existing record.</p>
        <div class="actions">
            <a class="button" href="<?= esc(site_url('customers'), 'attr') ?>">View Customers</a>
            <a href="<?= esc(site_url('customers/new'), 'attr') ?>">New Customer</a>
        </div>
    </section>
    <section class="panel" aria-labelledby="users-heading">
        <h2 id="users-heading">User Accounts</h2>
        <p class="muted">Manage staff names, usernames, roles, and profile pictures in one place.</p>
        <div class="actions">
            <a class="button" href="<?= esc(site_url('users'), 'attr') ?>">View Users</a>
            <a href="<?= esc(site_url('users/new'), 'attr') ?>">New User</a>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
