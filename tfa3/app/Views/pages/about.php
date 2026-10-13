<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div>
        <h1>About Simple POS</h1>
        <p class="page-description">A practical workspace for customer and staff account management.</p>
    </div>
</div>
<section class="panel content-panel" aria-labelledby="workspace-heading">
    <h2 id="workspace-heading">Your account workspace</h2>
    <p>Simple POS keeps customer contact details and staff profiles organized. Use Customer Accounts to add or update a customer's name, email, and phone number. Use User Accounts to maintain staff names, unique usernames, roles, and profile pictures.</p>
    <p class="muted">Customer and staff information is saved when you submit a valid form. Profile pictures are optional and can be added or replaced when editing a user.</p>
    <div class="actions">
        <a class="button" href="<?= esc(site_url('customers'), 'attr') ?>">View Customers</a>
        <a class="button button-secondary" href="<?= esc(site_url('users'), 'attr') ?>">View Users</a>
    </div>
</section>
<?= $this->endSection() ?>
