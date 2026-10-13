<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="form-container">
    <a class="back-link" href="<?= esc(site_url('users'), 'attr') ?>">Back to User Accounts</a>
    <div class="page-heading">
        <div>
            <h1><?= $isEdit ? 'Edit User' : 'New User' ?></h1>
            <p class="page-description"><?= $isEdit ? 'Update account details and the optional profile picture.' : 'Add a staff account to your team directory.' ?></p>
        </div>
    </div>
    <?php if ($errors !== []): ?>
        <div class="notice notice-error" role="alert">
            <p><?= esc($errors['general'] ?? 'Please correct the highlighted fields and try again.') ?></p>
            <?php if ($isEdit): ?><p>If you selected a profile picture, select the image again before saving. Your current profile picture has not changed.</p><?php endif ?>
        </div>
    <?php endif ?>
    <section class="panel" aria-label="User details">
        <p class="form-intro">Username and full name are required.</p>
        <form action="<?= esc(site_url($isEdit ? 'users/' . $id : 'users'), 'attr') ?>" method="post"<?= $isEdit ? ' enctype="multipart/form-data"' : '' ?>>
            <?= csrf_field() ?>
            <div class="form-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= esc($record['username'] ?? '', 'attr') ?>" maxlength="60" autocomplete="off" autocapitalize="none" spellcheck="false" required aria-describedby="username-help<?= isset($errors['username']) ? ' username-error' : '' ?>"<?= isset($errors['username']) ? ' aria-invalid="true"' : '' ?>>
                <p class="help-text" id="username-help">Choose a unique username for this account.</p>
                <?php if (isset($errors['username'])): ?><p class="field-error" id="username-error"><?= esc($errors['username']) ?></p><?php endif ?>
            </div>
            <div class="form-field">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?= esc($record['full_name'] ?? '', 'attr') ?>" maxlength="150" autocomplete="name" required<?= isset($errors['full_name']) ? ' aria-invalid="true" aria-describedby="full_name-error"' : '' ?>>
                <?php if (isset($errors['full_name'])): ?><p class="field-error" id="full_name-error"><?= esc($errors['full_name']) ?></p><?php endif ?>
            </div>
            <div class="form-field">
                <label for="role">Role <span class="optional">(optional)</span></label>
                <input type="text" id="role" name="role" value="<?= esc($record['role'] ?? '', 'attr') ?>" maxlength="60" autocomplete="off" aria-describedby="role-help<?= isset($errors['role']) ? ' role-error' : '' ?>"<?= isset($errors['role']) ? ' aria-invalid="true"' : '' ?>>
                <p class="help-text" id="role-help">For example, Cashier or Manager.</p>
                <?php if (isset($errors['role'])): ?><p class="field-error" id="role-error"><?= esc($errors['role']) ?></p><?php endif ?>
            </div>
            <?php if ($isEdit): ?>
                <div class="avatar-section">
                    <h2>Profile Picture</h2>
                    <div class="avatar-editor">
                        <img class="avatar avatar-preview" src="<?= esc(avatar_url($record['avatar'] ?? null), 'attr') ?>" alt="<?= esc(($record['full_name'] ?? 'User') . ' profile picture', 'attr') ?>" width="64" height="64">
                        <p class="help-text">Your current profile picture.<br>Leave the upload empty to keep it.</p>
                    </div>
                    <div class="form-field">
                        <label for="avatar">Choose Profile Picture <span class="optional">(optional)</span></label>
                        <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png" aria-describedby="avatar-help<?= isset($errors['avatar']) ? ' avatar-error' : '' ?>"<?= isset($errors['avatar']) ? ' aria-invalid="true"' : '' ?>>
                        <p class="help-text" id="avatar-help">JPG or PNG, up to 2 MB (2048 KB). Your image will be cropped to a centered, square profile picture.</p>
                        <?php if (isset($errors['avatar'])): ?><p class="field-error" id="avatar-error"><?= esc($errors['avatar']) ?></p><?php endif ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-actions">
                <button class="button" type="submit"><?= $isEdit ? 'Save Changes' : 'New User' ?></button>
                <a class="button button-secondary" href="<?= esc(site_url('users'), 'attr') ?>">Cancel</a>
            </div>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
