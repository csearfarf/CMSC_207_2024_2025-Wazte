
    <header class="header-global">
        <nav id="navbar-main"
            class="navbar navbar-main navbar-expand-lg headroom py-lg-3 px-lg-6 navbar-dark navbar-theme-primary">
            <div class="container d-flex flex-row-reverse">
                    <?php echo $googleButton;?>
            </div>
         

        </nav>
    </header>

    <main>

        <div class="preloader bg-soft flex-column justify-content-center align-items-center">
            <div class="loader-element">
                <img src="<?= base_url('public/front/assets/img/wazte_logo.png') ?>" height="120">
            </div>
        </div>

        <!-- Hero -->
        <section class="section-header pb-9 pb-lg-12 bg-primary text-white">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-12 col-sm-8 col-md-7 col-lg-6 text-center">
                        <img src="<?= base_url('public/front/assets/img/front_logo.png') ?>" class="mb-4" height="180" alt="Logo Impact">
                       
                        <h2 class=" text-muted mb-3 font-weight-bold">Waste and Zone Tracking Engine</h2>
                        <p class=" text-muted mb-5 font-weight-normal"> Navigating Waste into Sustainable
                            Opportunities.</p>

                      

                    </div>
                </div>
            </div>
            <div class="pattern bottom"></div>
        </section>
        <div class="section pt-0">
            <div class="container mt-n10 mt-lg-n12 z-2">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <img src="<?= base_url('public/front/assets/img/presentation-mockup.png') ?>" alt="illustration">
                    </div>
                </div>
            </div>
        </div>
        <section class="section section-lg pt-6">
            <div class="container">
                <div class="row justify-content-center mb-5 mb-md-7">
                    <div class="col-12 col-md-8 text-center">
                        <h2 class="h1 font-weight-bolder mb-4">What's inside?</h2>
                        <p class="lead">Cutting-edge platform that guides you to nearby recycling centers through an
                            interactive map, delivers up-to-date waste collection schedules, and connects you directly
                            with recycling facilitators. Experience a seamless and sustainable solution designed to
                            empower your eco-friendly lifestyle.</p>
                    </div>
                </div>
                <div class="row row-grid align-items-center mb-5 mb-md-7">
                    <div class="col-12 col-md-5">
                        <h2 class="font-weight-bolder mb-4">Interactive Map Locator</h2>
                        <p>Navigate your area with an interactive map that clearly displays nearby
                            recycling centers using precise markers.
                            Quickly visualize your eco-friendly options at a glance.</p>

                    </div>
                    <div class="col-12 col-md-6 ml-md-auto">
                        <img src="<?= base_url('public/front/assets/img/map.svg') ?>" alt="" class="img-80">
                    </div>
                </div>
                <div class="row row-grid align-items-center mb-5 mb-md-7">
                    <div class="col-12 col-md-5 order-md-2">
                        <h2 class="font-weight-bolder mb-4">Search by Material Type</h2>
                        <p>Filter recycling centers by the specific recyclable materials you need to
                            dispose of, such as
                            plastic or e-waste.
                            Easily narrow your search to find facilities that cater to your unique waste management
                            needs.</p>


                    </div>
                    <div class="col-12 col-md-6 mr-lg-auto">
                        <img src="<?= base_url('public/front/assets/img/search.svg') ?>" alt="" class="img-80">
                    </div>
                </div>
                <div class="row row-grid align-items-center mb-5 mb-md-7">
                    <div class="col-12 col-md-5">
                        <h2 class="font-weight-bolder mb-4">Contact Facilitators</h2>
                        <p>Engage directly with facility facilitators through an integrated contact form
                            and inquiry system.
                            Receive prompt answers to your questions and streamline your recycling experience.</p>


                    </div>
                    <div class="col-12 col-md-6 ml-md-auto">
                        <img src="<?= base_url('public/front/assets/img/contact.svg') ?>" alt="" class="img-80">
                    </div>
                </div>
            </div>
        </section>


        <footer class="footer section pt-6 pt-md-8 pt-lg-10 pb-3 bg-primary text-white overflow-hidden">
            <div class="pattern pattern-soft top"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 mb-6 mb-lg-0 text-center ">
                        <a class="footer-brand mr-lg-5 d-flex justify-content-center align-items-center"
                            href="./index.html">
                            <img src="<?= base_url('public/front/assets/img/front_logo.png') ?>" height="95" class="mr-3" alt="Footer logo">
                        </a>

                    </div>
                </div>
                <hr class="my-4 my-lg-5">
                <div class="row">
                    <div class="col pb-4 mb-md-0">
                        <div class="d-flex text-center justify-content-center align-items-center">
                            <p class="font-weight-normal mb-0">© Wazte
                                <span class="current-year"></span> All rights reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </main>
