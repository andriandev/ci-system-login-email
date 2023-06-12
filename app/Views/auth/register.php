<?= $this->extend('layout/main'); ?>

<?= $this->section('content'); ?>
<div class="row d-flex justify-content-center mt-5">
    <div class="col-12 col-lg-5">
        <h1 class="h3 text-center mb-3">Register</h1>
        <?php if (session()->getFlashdata('alert') && session()->getFlashdata('message')) : ?>
            <div class="alert alert-<?= session()->getFlashdata('alert'); ?>" role="alert">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>
        <div class="card card-body">
            <form action="/register" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control <?= isset($validation['email']) ? 'is-invalid' : '' ?>" id="email" autocomplete="off" value="<?= old('email'); ?>" required>
                    <div id="validationEmail" class="invalid-feedback">
                        <?= isset($validation['email']) ? $validation['email'] : '' ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control <?= isset($validation['name']) ? 'is-invalid' : '' ?>" id="name" autocomplete="off" value="<?= old('name'); ?>" required>
                    <div id="validationName" class="invalid-feedback">
                        <?= isset($validation['name']) ? $validation['name'] : '' ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control <?= isset($validation['password']) ? 'is-invalid' : '' ?>" id="password" autocomplete="off" value="<?= old('password'); ?>" required>
                    <div id="validationPassword" class="invalid-feedback">
                        <?= isset($validation['password']) ? $validation['password'] : '' ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Profile picture</label>
                    <input class="form-control <?= isset($validation['image']) ? 'is-invalid' : '' ?>" name="image" type="file" id="image" autocomplete="off">
                    <div id="validationImage" class="invalid-feedback">
                        <?= isset($validation['image']) ? $validation['image'] : '' ?>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>