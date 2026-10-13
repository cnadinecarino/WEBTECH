<?= $this->include('layout/header') ?>
<section class="page-heading"><div><p class="eyebrow">ACCOUNT</p><h1>Profile</h1><p class="lede">The demo account stored for this workspace.</p></div></section>
<?php if ($user === null): ?>
    <div class="empty-state"><h3>Profile unavailable</h3><p>No demo user was found. Run the database seeder to add the sample profile.</p></div>
<?php else: ?>
    <?php $initials = implode('', array_map(static fn ($part) => strtoupper(substr($part, 0, 1)), array_slice(explode(' ', $user['full_name']), 0, 2))); ?>
    <section class="profile-card"><div class="profile-top"><div class="avatar" aria-hidden="true"><?= esc($initials) ?></div><div><p class="eyebrow">DEMO USER</p><h2><?= esc($user['full_name']) ?></h2><p class="username">@<?= esc($user['username']) ?></p></div></div><div class="profile-fields"><div><span>Username</span><strong><?= esc($user['username']) ?></strong></div><div><span>Full name</span><strong><?= esc($user['full_name']) ?></strong></div><div><span>Email address</span><strong><?= esc($user['email']) ?></strong></div><div><span>Account created</span><strong><?= esc(date('F j, Y', strtotime($user['created_at']))) ?></strong></div></div></section>
<?php endif ?>
<?= $this->include('layout/footer') ?>
