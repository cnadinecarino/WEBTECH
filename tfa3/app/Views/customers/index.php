<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div>
        <h1>Customer Accounts</h1>
        <p class="page-description">Manage customer names and contact information.</p>
    </div>
    <a class="button" href="<?= esc(site_url('customers/new'), 'attr') ?>">New Customer</a>
</div>
<section class="panel table-panel" aria-label="Customer directory">
    <?php if ($customers === []): ?>
        <div class="empty-state">
            <h2>No customers yet</h2>
            <p>Add your first customer to start building your customer directory.</p>
            <a class="button" href="<?= esc(site_url('customers/new'), 'attr') ?>">New Customer</a>
        </div>
    <?php else: ?>
        <div class="table-wrap" role="region" aria-label="Customer accounts table; scroll horizontally on small screens" tabindex="0">
            <table>
                <caption class="sr-only">Customer contact information and edit actions</caption>
                <thead>
                    <tr>
                        <th scope="col">Full Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col" class="cell-action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td class="cell-name"><?= esc($customer['full_name']) ?></td>
                            <td class="cell-email"><?= esc($customer['email']) ?></td>
                            <td class="cell-phone"><?= esc($customer['phone'] ?: '—') ?></td>
                            <td class="cell-action"><a class="edit-link" href="<?= esc(site_url('customers/' . $customer['id'] . '/edit'), 'attr') ?>" aria-label="<?= esc('Edit ' . $customer['full_name'], 'attr') ?>">Edit</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
