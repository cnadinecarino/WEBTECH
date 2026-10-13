<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="form-container">
    <a class="back-link" href="<?= esc(site_url('customers'), 'attr') ?>">Back to Customer Accounts</a>
    <div class="page-heading">
        <div>
            <h1><?= $isEdit ? 'Edit Customer' : 'New Customer' ?></h1>
            <p class="page-description"><?= $isEdit ? 'Update the contact information for this customer.' : 'Add a customer to your contact directory.' ?></p>
        </div>
    </div>
    <?php if ($errors !== []): ?>
        <div class="notice notice-error" role="alert">
            <p><?= esc($errors['general'] ?? 'Please correct the highlighted fields and try again.') ?></p>
        </div>
    <?php endif ?>
    <section class="panel" aria-label="Customer details">
        <p class="form-intro">Full name and email are required.</p>
        <form action="<?= esc(site_url($isEdit ? 'customers/' . $id : 'customers'), 'attr') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-field">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?= esc($record['full_name'] ?? '', 'attr') ?>" maxlength="150" autocomplete="name" required<?= isset($errors['full_name']) ? ' aria-invalid="true" aria-describedby="full_name-error"' : '' ?>>
                <?php if (isset($errors['full_name'])): ?><p class="field-error" id="full_name-error"><?= esc($errors['full_name']) ?></p><?php endif ?>
            </div>
            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= esc($record['email'] ?? '', 'attr') ?>" maxlength="254" autocomplete="email" required<?= isset($errors['email']) ? ' aria-invalid="true" aria-describedby="email-error"' : '' ?>>
                <?php if (isset($errors['email'])): ?><p class="field-error" id="email-error"><?= esc($errors['email']) ?></p><?php endif ?>
            </div>
            <div class="form-field">
                <label for="phone">Phone <span class="optional">(optional)</span></label>
                <input type="tel" id="phone" name="phone" value="<?= esc($record['phone'] ?? '', 'attr') ?>" maxlength="40" autocomplete="tel"<?= isset($errors['phone']) ? ' aria-invalid="true" aria-describedby="phone-error"' : '' ?>>
                <?php if (isset($errors['phone'])): ?><p class="field-error" id="phone-error"><?= esc($errors['phone']) ?></p><?php endif ?>
            </div>
            <div class="form-actions">
                <button class="button" type="submit"><?= $isEdit ? 'Save Changes' : 'New Customer' ?></button>
                <a class="button button-secondary" href="<?= esc(site_url('customers'), 'attr') ?>">Cancel</a>
            </div>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
