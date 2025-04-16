<section class="vh-100 d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-6 order-2 order-lg-1 text-center text-lg-left">
                <h1 class="mt-5">Unauthorized Access</h1>
                <p class="lead my-4">
                    It appears you do not have permission to view this page. Please login or contact your administrator if you believe this is an error.
                </p>
                <a class="btn btn-secondary animate-hover" href="<?= base_url('login') ?>">
                    <i class="fas fa-chevron-left mr-3 pl-2 animate-left-3"></i>
                    Return to Page
                </a>
            </div>
            <div class="col-12 col-lg-6 order-1 order-lg-2 text-center d-flex align-items-center justify-content-center">
                <img class="img-fluid w-75" src="<?= base_url('public/warning.svg') ?>" alt="401 Unauthorized Error">
            </div>
        </div>
    </div>
</section>
