<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h1>Customer Accounts</h1>
<p class="lead">Customer records retrieved from the POS database.</p>

<div class="table-wrapper">
    <table>
        <caption class="visually-hidden">Customer account records</caption>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($customers === []): ?>
                <tr>
                    <td class="empty-state" colspan="4">No customer accounts are available yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= esc($customer['full_name']) ?></td>
                        <td><a class="table-link" href="mailto:<?= esc($customer['email'], 'attr') ?>"><?= esc($customer['email']) ?></a></td>
                        <td><?= $customer['phone'] !== null && $customer['phone'] !== '' ? esc($customer['phone']) : '<span class="muted">Not provided</span>' ?></td>
                        <td><time datetime="<?= esc($customer['created_at'], 'attr') ?>"><?= esc(date('M j, Y', strtotime($customer['created_at']))) ?></time></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
