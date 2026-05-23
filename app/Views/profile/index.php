<?= $this->extend('layout/main'); ?>

<?= $this->section('content'); ?>

<div class="pagetitle">
    <h1>Profile</h1>
</div>

<section class="section profile">
    <div class="row">

        <div class="col-xl-4">

            <div class="card">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                    <img src="<?= base_url('NiceAdmin/assets/img/profile-img.jpg') ?>" 
                         alt="Profile"
                         class="rounded-circle">

                    <h2>Widya</h2>
                    <h3>Student</h3>

                </div>
            </div>

        </div>

        <div class="col-xl-8">

            <div class="card">
                <div class="card-body pt-3">

                    <h5 class="card-title">Profile Details</h5>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 label">Full Name</div>
                        <div class="col-lg-9 col-md-8">Widya</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 label">Email</div>
                        <div class="col-lg-9 col-md-8">widya@gmail.com</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 label">Phone</div>
                        <div class="col-lg-9 col-md-8">081234567890</div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>

<?= $this->endSection(); ?>