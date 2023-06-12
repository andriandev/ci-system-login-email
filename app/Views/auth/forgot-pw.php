<?= $this->extend('layout/main'); ?>

<?= $this->section('content'); ?>
<div class="row d-flex justify-content-center mt-5">
    <div class="col-12 col-lg-5">
        <?php if ($status == 'forgot-pw') : ?>
            <h1 class="h3 text-center mb-3">Forgot Password</h1>
            <?php if (session()->getFlashdata('alert') && session()->getFlashdata('message')) : ?>
                <div class="alert alert-<?= session()->getFlashdata('alert'); ?>" role="alert">
                    <?= session()->getFlashdata('message'); ?>
                </div>
            <?php endif; ?>
            <div class="card card-body">
                <form action="/forgot-password" method="post">
                    <?= csrf_field(); ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control <?= isset($validation['email']) ? 'is-invalid' : '' ?>" id="email" autocomplete="off">
                        <div id="validationEmail" class="invalid-feedback">
                            <?= isset($validation['email']) ? $validation['email'] : '' ?>
                        </div>
                        <div id="emailHelp" class="form-text">Enter your email address to get a password reset link.</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($status == 'new-pw') : ?>
            <h1 class="h3 text-center mb-3">New Password</h1>
            <div class="card card-body">
                <form action="/reset-password" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="token" value="<?= $_GET['token']; ?>">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" name="password" class="form-control <?= isset($validation['password']) ? 'is-invalid' : '' ?>" id="password" autocomplete="off">
                        <div id="passwordHelp" class="form-text">Enter your new password.</div>
                        <div id="validationPassword" class="invalid-feedback">
                            <?= isset($validation['password']) ? $validation['password'] : '' ?>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection(); ?>