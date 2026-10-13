<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="panel">
    <h1>Accounts are temporarily unavailable</h1>
    <p>We could not load the account records. Please try again later or contact the application administrator.</p>
    <a class="button" href="<?= esc(site_url(), 'attr') ?>">Return Home</a>
</section>
<?= $this->endSection() ?>
