<?= $this->include('templates/header') ?>

<section class="card">
    <h1>Customer Accounts</h1>
    <p class="lead">Fictional customer records maintained by the POS system.</p>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th scope="col">Full Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= esc($customer['full_name']) ?></td>
                        <td><?= esc($customer['email']) ?></td>
                        <td><?= esc($customer['phone']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>

<?= $this->include('templates/footer') ?>
