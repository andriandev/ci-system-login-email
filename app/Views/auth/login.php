<?= $this->extend('layout/main'); ?>

<?= $this->section('content'); ?>
<div class="row d-flex justify-content-center mt-5">
    <div class="col-12 col-lg-5">
        <h1 class="h3 text-center mb-3">Login</h1>
        <?php if (session()->getFlashdata('alert') && session()->getFlashdata('message')) : ?>
            <div class="alert alert-<?= session()->getFlashdata('alert'); ?>" role="alert">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>
        <div class="card card-body">
            <form action="/login" method="post">
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control <?= isset($validation['email']) ? 'is-invalid' : '' ?>" id="email" autocomplete="off" value="<?= old('email'); ?>" required>
                    <div id="validationEmail" class="invalid-feedback">
                        <?= isset($validation['email']) ? $validation['email'] : '' ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control <?= isset($validation['password']) ? 'is-invalid' : '' ?>" id="password" autocomplete="off" value="<?= old('password'); ?>" required>
                    <div id="validationPassword" class="invalid-feedback">
                        <?= isset($validation['password']) ? $validation['password'] : '' ?>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
        <div class="my-3 text-center">
            <a href="/forgot-password" class="text-decoration-none">Forgot Password ?</a>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>