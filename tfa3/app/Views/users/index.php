<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-heading">
    <div>
        <h1>User Accounts</h1>
        <p class="page-description">Manage staff profiles and account details.</p>
    </div>
    <a class="button" href="<?= esc(site_url('users/new'), 'attr') ?>">New User</a>
</div>
<section class="panel table-panel" aria-label="Staff directory">
    <?php if ($users === []): ?>
        <div class="empty-state">
            <h2>No users yet</h2>
            <p>Create the first staff account to start your team directory.</p>
            <a class="button" href="<?= esc(site_url('users/new'), 'attr') ?>">New User</a>
        </div>
    <?php else: ?>
        <div class="table-wrap" role="region" aria-label="User accounts table; scroll horizontally on small screens" tabindex="0">
            <table>
                <caption class="sr-only">Staff profile pictures, account details, and edit actions</caption>
                <thead>
                    <tr>
                        <th scope="col"><span class="sr-only">Profile Picture</span></th>
                        <th scope="col">Username</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Role</th>
                        <th scope="col" class="cell-action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="avatar-cell"><img class="avatar" src="<?= esc(avatar_url($user['avatar'] ?? null), 'attr') ?>" alt="<?= esc($user['full_name'] . ' profile picture', 'attr') ?>" width="48" height="48" loading="lazy"></td>
                            <td class="cell-name"><?= esc($user['username']) ?></td>
                            <td class="cell-name"><?= esc($user['full_name']) ?></td>
                            <td><?php if ($user['role'] !== ''): ?><span class="role-label"><?= esc($user['role']) ?></span><?php else: ?><span class="muted">—</span><?php endif ?></td>
                            <td class="cell-action"><a class="edit-link" href="<?= esc(site_url('users/' . $user['id'] . '/edit'), 'attr') ?>" aria-label="<?= esc('Edit ' . $user['full_name'], 'attr') ?>">Edit</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
