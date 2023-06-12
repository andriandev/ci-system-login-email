<?= $this->extend('layout/main'); ?>

<?= $this->section('content'); ?>
<h1 class="h3">Home</h1>
<p>System login with email verify created by AndrianDev.</p>
<?php if (session()->get('name')) : ?>
    <p>Hello <?= session()->get('name'); ?> welcome back to my website.</p>
<?php endif; ?>
<?php if (session()->getFlashdata('alert') && session()->getFlashdata('message')) : ?>
    <div class="alert alert-<?= session()->getFlashdata('alert'); ?>" role="alert">
        <?= session()->getFlashdata('message'); ?>
    </div>
<?php endif; ?>
<?= $this->endSection(); ?>