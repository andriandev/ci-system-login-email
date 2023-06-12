<?= $this->extend('layout/main'); ?>

<?= $this->section('content'); ?>
<h1 class="h3">Profile</h1>
<?php if (session()->getFlashdata('alert') && session()->getFlashdata('message')) : ?>
    <div class="alert alert-<?= session()->getFlashdata('alert'); ?>" role="alert">
        <?= session()->getFlashdata('message'); ?>
    </div>
<?php endif; ?>
<div class="card">
    <div class="card-body">
        <div class="text-center mb-3">
            <img src="<?= base_url('/assets/img/' . $user['image']); ?>" class="img-thumbnail" width="500" alt="Avatar">
        </div>
        <ul class="list-group">
            <li class="list-group-item"><b>Email :</b> <?= $user['email']; ?></li>
            <li class="list-group-item"><b>Name :</b> <?= $user['name']; ?></li>
            <li class="list-group-item"><b>Created at :</b> <?= $user['created_at']; ?></li>
            <li class="list-group-item"><b>Updated at :</b> <?= $user['updated_at']; ?></li>
        </ul>
    </div>
</div>
<?= $this->endSection(); ?>